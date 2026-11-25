"""API RESTful de perfis administrativos do AFDE.

Execute na raiz do projeto com:
    python api/admin_api.py

A API reutiliza a tabela `usuarios` do sistema PHP e aceita somente tokens
HMAC emitidos pelo painel admin. O segredo deve ser o mesmo em PHP e Flask.
"""
from __future__ import annotations

import base64
import binascii
import hashlib
import hmac
import json
import os
import time
from functools import wraps
from pathlib import Path
from typing import Any, Callable

import bcrypt
import pymysql
from dotenv import load_dotenv
from flask import Flask, jsonify, request
from flask_cors import CORS

BASE_DIR = Path(__file__).resolve().parent.parent
load_dotenv(BASE_DIR / ".env")

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = 64 * 1024

allowed_origins = [origin.strip() for origin in os.getenv(
    "ADMIN_API_ALLOWED_ORIGINS",
    "http://localhost,http://127.0.0.1,http://localhost:80,http://127.0.0.1:80",
).split(",") if origin.strip()]
CORS(app, resources={r"/api/(admins|users).*": {"origins": allowed_origins}})

API_SECRET = os.getenv("ADMIN_API_SECRET", "")
TOKEN_TTL = 10 * 60


def database_connection():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        port=int(os.getenv("DB_PORT", "3306")),
        user=os.getenv("DB_USER", "root"),
        password=os.getenv("DB_PASS", ""),
        database=os.getenv("DB_NAME", "educacaofinanceira"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )


def json_error(message: str, status: int):
    return jsonify({"erro": message}), status


def b64url(value: bytes) -> str:
    return base64.urlsafe_b64encode(value).rstrip(b"=").decode("ascii")


def b64url_decode(value: str) -> bytes:
    return base64.urlsafe_b64decode(value + "=" * (-len(value) % 4))


def verify_admin_token(token: str) -> int | None:
    if not API_SECRET or not token:
        return None
    try:
        encoded_payload, encoded_signature = token.split(".", 1)
        expected = hmac.new(API_SECRET.encode(), encoded_payload.encode(), hashlib.sha256).digest()
        if not hmac.compare_digest(b64url(expected), encoded_signature):
            return None
        payload = json.loads(b64url_decode(encoded_payload))
        admin_id = int(payload["sub"])
        if int(payload["exp"]) < int(time.time()):
            return None
        return admin_id
    except (ValueError, TypeError, KeyError, json.JSONDecodeError, binascii.Error):
        return None


def require_admin(view: Callable[..., Any]):
    @wraps(view)
    def wrapped(*args, **kwargs):
        authorization = request.headers.get("Authorization", "")
        token = authorization[7:].strip() if authorization.lower().startswith("bearer ") else ""
        admin_id = verify_admin_token(token)
        if not admin_id:
            return json_error("Autenticação administrativa inválida ou expirada.", 401)
        try:
            with database_connection() as db:
                with db.cursor() as cursor:
                    cursor.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='admin'", (admin_id,))
                    if not cursor.fetchone():
                        return json_error("Administrador não encontrado ou sem permissão.", 403)
        except pymysql.MySQLError:
            app.logger.exception("Falha ao validar administrador")
            return json_error("Não foi possível acessar o banco de dados.", 503)
        return view(*args, **kwargs)
    return wrapped


def request_data() -> dict[str, Any]:
    data = request.get_json(silent=True)
    return data if isinstance(data, dict) else {}


def validate_profile(data: dict[str, Any], password_required: bool = True):
    nome = str(data.get("nome", "")).strip()
    email = str(data.get("email", "")).strip().lower()
    telefone = str(data.get("telefone", "")).strip()
    senha = str(data.get("senha", ""))
    errors: list[str] = []
    if len(nome) < 2 or len(nome) > 120:
        errors.append("O nome deve ter entre 2 e 120 caracteres.")
    if "@" not in email or len(email) > 180:
        errors.append("Informe um e-mail válido.")
    senha_bytes = len(senha.encode("utf-8"))
    if password_required and len(senha) < 8:
        errors.append("A senha deve ter pelo menos 8 caracteres.")
    if not password_required and senha and len(senha) < 8:
        errors.append("A nova senha deve ter pelo menos 8 caracteres.")
    if senha and senha_bytes > 72:
        errors.append("A senha deve ter no máximo 72 bytes (limite do bcrypt).")
    if len(telefone) > 30:
        errors.append("O telefone deve ter no máximo 30 caracteres.")
    return {"nome": nome, "email": email, "telefone": telefone, "senha": senha}, errors


def public_profile(row: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": int(row["id"]),
        "nome": row["nome"],
        "email": row["email"],
        "telefone": row.get("telefone") or "",
        "provedor": row.get("provedor") or "local",
        "tipo": row.get("tipo") or "admin",
        "data_criacao": row.get("data_criacao").isoformat(sep=" ") if row.get("data_criacao") else None,
    }


@app.get("/api/admins")
@require_admin
def list_admins():
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("""SELECT id, nome, email, telefone, provedor, tipo, data_criacao
                                 FROM usuarios WHERE tipo='admin' ORDER BY data_criacao DESC, id DESC""")
                return jsonify({"dados": [public_profile(row) for row in cursor.fetchall()]})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao listar administradores")
        return json_error("Não foi possível listar os administradores.", 503)


@app.post("/api/admins")
@require_admin
def create_admin():
    data, errors = validate_profile(request_data(), password_required=True)
    if errors:
        return jsonify({"erros": errors}), 400
    password_hash = bcrypt.hashpw(data["senha"].encode(), bcrypt.gensalt()).decode()
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE email=%s LIMIT 1", (data["email"],))
                if cursor.fetchone():
                    return json_error("Já existe um usuário com este e-mail.", 409)
                cursor.execute("""INSERT INTO usuarios (itemid, nome, email, foto, senha, telefone,
                                  provedor, tipo, data_criacao)
                                  VALUES ('', %s, %s, NULL, %s, %s, 'local', 'admin', NOW())""",
                               (data["nome"], data["email"], password_hash, data["telefone"]))
                new_id = cursor.lastrowid
                db.commit()
                cursor.execute("SELECT id, nome, email, telefone, provedor, tipo, data_criacao FROM usuarios WHERE id=%s", (new_id,))
                return jsonify({"mensagem": "Perfil de administrador criado.", "dado": public_profile(cursor.fetchone())}), 201
    except pymysql.MySQLError:
        app.logger.exception("Falha ao criar administrador")
        return json_error("Não foi possível criar o administrador.", 503)


@app.put("/api/admins/<int:admin_id>")
@require_admin
def update_admin(admin_id: int):
    data, errors = validate_profile(request_data(), password_required=False)
    if errors:
        return jsonify({"erros": errors}), 400
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='admin'", (admin_id,))
                if not cursor.fetchone():
                    return json_error("Administrador não encontrado.", 404)
                cursor.execute("SELECT id FROM usuarios WHERE email=%s AND id<>%s LIMIT 1", (data["email"], admin_id))
                if cursor.fetchone():
                    return json_error("Já existe um usuário com este e-mail.", 409)
                if data["senha"]:
                    password_hash = bcrypt.hashpw(data["senha"].encode(), bcrypt.gensalt()).decode()
                    cursor.execute("UPDATE usuarios SET nome=%s, email=%s, telefone=%s, senha=%s WHERE id=%s",
                                   (data["nome"], data["email"], data["telefone"], password_hash, admin_id))
                else:
                    cursor.execute("UPDATE usuarios SET nome=%s, email=%s, telefone=%s WHERE id=%s",
                                   (data["nome"], data["email"], data["telefone"], admin_id))
                db.commit()
                cursor.execute("SELECT id, nome, email, telefone, provedor, tipo, data_criacao FROM usuarios WHERE id=%s", (admin_id,))
                return jsonify({"mensagem": "Perfil de administrador atualizado.", "dado": public_profile(cursor.fetchone())})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao atualizar administrador")
        return json_error("Não foi possível atualizar o administrador.", 503)


@app.delete("/api/admins/<int:admin_id>")
@require_admin
def delete_admin(admin_id: int):
    authorization = request.headers.get("Authorization", "")
    requester_id = verify_admin_token(authorization[7:].strip()) if authorization.lower().startswith("bearer ") else None
    if requester_id == admin_id:
        return json_error("Você não pode excluir a própria conta.", 400)
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='admin'", (admin_id,))
                if not cursor.fetchone():
                    return json_error("Administrador não encontrado.", 404)
                cursor.execute("SELECT COUNT(*) AS total FROM usuarios WHERE tipo='admin'")
                if int(cursor.fetchone()["total"]) <= 1:
                    return json_error("Mantenha pelo menos um administrador no sistema.", 409)
                cursor.execute("DELETE FROM usuarios WHERE id=%s AND tipo='admin'", (admin_id,))
                db.commit()
                return jsonify({"mensagem": "Perfil de administrador excluído."})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao excluir administrador")
        return json_error("Não foi possível excluir o administrador.", 503)


@app.get("/api/users")
@require_admin
def list_users():
    """Lista somente contas comuns; a rota nunca retorna administradores."""
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("""SELECT id, nome, email, telefone, provedor, tipo, data_criacao
                                 FROM usuarios WHERE tipo='usuario' ORDER BY data_criacao DESC, id DESC""")
                return jsonify({"dados": [public_profile(row) for row in cursor.fetchall()]})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao listar usuários comuns")
        return json_error("Não foi possível listar os usuários comuns.", 503)


@app.post("/api/users")
@require_admin
def create_user():
    data, errors = validate_profile(request_data(), password_required=True)
    if errors:
        return jsonify({"erros": errors}), 400
    password_hash = bcrypt.hashpw(data["senha"].encode(), bcrypt.gensalt()).decode()
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE email=%s LIMIT 1", (data["email"],))
                if cursor.fetchone():
                    return json_error("Já existe um usuário com este e-mail.", 409)
                cursor.execute("""INSERT INTO usuarios (itemid, nome, email, foto, senha, telefone,
                                  provedor, tipo, data_criacao)
                                  VALUES ('', %s, %s, NULL, %s, %s, 'local', 'usuario', NOW())""",
                               (data["nome"], data["email"], password_hash, data["telefone"]))
                new_id = cursor.lastrowid
                db.commit()
                cursor.execute("SELECT id, nome, email, telefone, provedor, tipo, data_criacao FROM usuarios WHERE id=%s AND tipo='usuario'", (new_id,))
                return jsonify({"mensagem": "Usuário comum criado.", "dado": public_profile(cursor.fetchone())}), 201
    except pymysql.MySQLError:
        app.logger.exception("Falha ao criar usuário comum")
        return json_error("Não foi possível criar o usuário comum.", 503)


@app.put("/api/users/<int:user_id>")
@require_admin
def update_user(user_id: int):
    data, errors = validate_profile(request_data(), password_required=False)
    if errors:
        return jsonify({"erros": errors}), 400
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='usuario'", (user_id,))
                if not cursor.fetchone():
                    return json_error("Usuário comum não encontrado.", 404)
                cursor.execute("SELECT id FROM usuarios WHERE email=%s AND id<>%s LIMIT 1", (data["email"], user_id))
                if cursor.fetchone():
                    return json_error("Já existe um usuário com este e-mail.", 409)
                if data["senha"]:
                    password_hash = bcrypt.hashpw(data["senha"].encode(), bcrypt.gensalt()).decode()
                    cursor.execute("UPDATE usuarios SET nome=%s, email=%s, telefone=%s, senha=%s WHERE id=%s AND tipo='usuario'",
                                   (data["nome"], data["email"], data["telefone"], password_hash, user_id))
                else:
                    cursor.execute("UPDATE usuarios SET nome=%s, email=%s, telefone=%s WHERE id=%s AND tipo='usuario'",
                                   (data["nome"], data["email"], data["telefone"], user_id))
                db.commit()
                cursor.execute("SELECT id, nome, email, telefone, provedor, tipo, data_criacao FROM usuarios WHERE id=%s AND tipo='usuario'", (user_id,))
                return jsonify({"mensagem": "Usuário comum atualizado.", "dado": public_profile(cursor.fetchone())})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao atualizar usuário comum")
        return json_error("Não foi possível atualizar o usuário comum.", 503)


@app.delete("/api/users/<int:user_id>")
@require_admin
def delete_user(user_id: int):
    try:
        with database_connection() as db:
            with db.cursor() as cursor:
                cursor.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='usuario'", (user_id,))
                if not cursor.fetchone():
                    return json_error("Usuário comum não encontrado.", 404)
                cursor.execute("DELETE FROM usuarios WHERE id=%s AND tipo='usuario'", (user_id,))
                db.commit()
                return jsonify({"mensagem": "Usuário comum excluído."})
    except pymysql.MySQLError:
        app.logger.exception("Falha ao excluir usuário comum")
        return json_error("Não foi possível excluir o usuário comum.", 503)


@app.errorhandler(413)
def request_too_large(_error):
    return json_error("A requisição excede o limite permitido.", 413)


if __name__ == "__main__":
    app.run(host=os.getenv("ADMIN_API_HOST", "127.0.0.1"), port=int(os.getenv("ADMIN_API_PORT", "5001")), debug=os.getenv("DEBUG", "0") == "1")
