"""API administrativa Flask para o projeto AFDE.

A API compartilha a tabela usuarios com o backend PHP. Ela não expõe
credenciais: toda operação exige um token HMAC de curta duração emitido pela
página PHP já protegida por sessão de administrador.
"""
from __future__ import annotations

import base64
import hashlib
import hmac
import json
import os
import re
import time
from collections import defaultdict
from datetime import datetime, timezone
from functools import wraps
from typing import Any, Callable

import bcrypt
import pymysql
from dotenv import load_dotenv
from flask import Flask, jsonify, request, send_from_directory
from flask_cors import CORS
from werkzeug.exceptions import HTTPException

load_dotenv(os.path.join(os.path.dirname(__file__), "..", ".env"))

app = Flask(__name__)
app.config["JSON_AS_ASCII"] = False
app.config["MAX_CONTENT_LENGTH"] = 16 * 1024

ALLOWED_ORIGINS = [
    item.strip()
    for item in os.getenv(
        "ALLOWED_ORIGIN",
        "http://localhost,http://127.0.0.1,http://localhost:80,http://127.0.0.1:80",
    ).split(",")
    if item.strip()
]
CORS(app, resources={r"/api/*": {"origins": ALLOWED_ORIGINS}})

TOKEN_SECRET = os.getenv("AFDE_ADMIN_API_SECRET", "").strip()
IS_PRODUCTION = os.getenv("AFDE_ENV", "development").lower() == "production"
if IS_PRODUCTION and len(TOKEN_SECRET) < 32:
    raise RuntimeError("AFDE_ADMIN_API_SECRET deve ter pelo menos 32 caracteres em produção.")
if not TOKEN_SECRET:
    # Fallback apenas para desenvolvimento local. Nunca use esse valor em produção.
    TOKEN_SECRET = "dev-only-change-this-secret-before-production-8f3c"
TOKEN_TTL = 15 * 60
EMAIL_RE = re.compile(r"^[^@\s]+@[^@\s]+\.[^@\s]+$")
ALLOWED_FIELDS = {"nome", "email", "telefone", "tipo"}
_RATE_LIMIT = defaultdict(list)


def b64url(value: bytes) -> str:
    return base64.urlsafe_b64encode(value).rstrip(b"=").decode("ascii")


def db_connection():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        port=int(os.getenv("DB_PORT", "3307")),
        user=os.getenv("DB_USER", "root"),
        password=os.getenv("DB_PASS", ""),
        database=os.getenv("DB_NAME", "educacaofinanceira"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
        connect_timeout=5,
        read_timeout=10,
        write_timeout=10,
    )


def issue_token(admin_id: int) -> str:
    payload = {"sub": int(admin_id), "exp": int(datetime.now(timezone.utc).timestamp()) + TOKEN_TTL}
    encoded = b64url(json.dumps(payload, separators=(",", ":")).encode())
    signature = hmac.new(TOKEN_SECRET.encode(), encoded.encode(), hashlib.sha256).digest()
    return f"{encoded}.{b64url(signature)}"


def verify_token(token: str) -> dict[str, Any] | None:
    try:
        encoded, signature = token.split(".")
        if not encoded or not signature or len(signature) != 43:
            return None
        expected = b64url(hmac.new(TOKEN_SECRET.encode(), encoded.encode(), hashlib.sha256).digest())
        if not hmac.compare_digest(signature, expected):
            return None
        padding = "=" * (-len(encoded) % 4)
        payload = json.loads(base64.urlsafe_b64decode((encoded + padding).encode()))
        if int(payload["exp"]) < int(datetime.now(timezone.utc).timestamp()):
            return None
        return payload
    except (ValueError, KeyError, TypeError, json.JSONDecodeError, UnicodeDecodeError):
        return None


def is_rate_limited(key: str, limit: int = 120, window: int = 60) -> bool:
    now = time.monotonic()
    recent = [stamp for stamp in _RATE_LIMIT[key] if now - stamp < window]
    if len(recent) >= limit:
        _RATE_LIMIT[key] = recent
        return True
    recent.append(now)
    _RATE_LIMIT[key] = recent
    return False


@app.after_request
def security_headers(response):
    response.headers.setdefault("X-Content-Type-Options", "nosniff")
    response.headers.setdefault("X-Frame-Options", "DENY")
    response.headers.setdefault("Referrer-Policy", "no-referrer")
    response.headers.setdefault("Cache-Control", "no-store")
    response.headers.setdefault("Permissions-Policy", "camera=(), microphone=(), geolocation=()")
    return response


def admin_required(view: Callable):
    @wraps(view)
    def wrapped(*args, **kwargs):
        client_key = request.remote_addr or "unknown"
        if is_rate_limited(f"admin:{client_key}"):
            return jsonify(ok=False, message="Muitas solicitações. Tente novamente em instantes."), 429
        authorization = request.headers.get("Authorization", "")
        if not authorization.startswith("Bearer "):
            return jsonify(ok=False, message="Token de administrador ausente."), 401
        payload = verify_token(authorization[7:].strip())
        if not payload:
            return jsonify(ok=False, message="Token inválido ou expirado."), 401
        try:
            with db_connection() as db, db.cursor() as cur:
                cur.execute("SELECT id FROM usuarios WHERE id=%s AND tipo='admin' LIMIT 1", (int(payload["sub"]),))
                if not cur.fetchone():
                    return jsonify(ok=False, message="Acesso administrativo revogado."), 403
        except pymysql.MySQLError:
            return jsonify(ok=False, message="Não foi possível acessar o banco de dados."), 503
        return view(*args, **kwargs)

    return wrapped


def serialize_user(row: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": int(row["id"]),
        "nome": row["nome"],
        "email": row["email"],
        "telefone": row.get("telefone") or "",
        "provedor": row.get("provedor") or "local",
        "tipo": row["tipo"],
        "data_criacao": row["data_criacao"].isoformat(sep=" ") if hasattr(row.get("data_criacao"), "isoformat") else row.get("data_criacao"),
    }


def php_password_hash(password: str) -> str:
    """Gera bcrypt e normaliza o identificador para o formato PHP $2y$."""
    return bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode().replace("$2b$", "$2y$", 1)


def read_payload() -> dict[str, Any]:
    data = request.get_json(silent=True)
    if not isinstance(data, dict):
        raise ValueError("Envie um corpo JSON válido.")
    return data


def validate_fields(data: dict[str, Any], creating: bool = False) -> tuple[dict[str, Any], str | None]:
    unknown = set(data) - ALLOWED_FIELDS - {"senha"}
    if unknown:
        return {}, f"Campos não permitidos: {', '.join(sorted(unknown))}."
    result: dict[str, Any] = {}
    for field in ("nome", "email", "telefone", "tipo"):
        if field in data:
            value = str(data[field]).strip()
            if field == "nome" and not 2 <= len(value) <= 255:
                return {}, "O nome deve ter entre 2 e 255 caracteres."
            if field == "email" and (not EMAIL_RE.match(value) or len(value) > 190):
                return {}, "Informe um e-mail válido."
            if field == "telefone" and len(value) > 25:
                return {}, "O telefone deve ter no máximo 25 caracteres."
            if field == "tipo" and value not in {"admin", "usuario"}:
                return {}, "O tipo deve ser admin ou usuario."
            result[field] = value
    if creating and not {"nome", "email", "tipo"}.issubset(result):
        return {}, "nome, email e tipo são obrigatórios."
    if creating and "senha" not in result:
        return {}, "A senha é obrigatória ao criar uma conta."
    if "senha" in data:
        senha = str(data["senha"])
        if creating and len(senha) < 8:
            return {}, "A senha deve ter pelo menos 8 caracteres."
        if not creating and senha and len(senha) < 8:
            return {}, "A senha deve ter pelo menos 8 caracteres."
        if senha:
            result["senha"] = senha
    return result, None


@app.get("/health")
def health():
    return jsonify(ok=True, service="afde-admin-api")


@app.get("/docs")
def swagger_ui():
    return send_from_directory(os.path.join(os.path.dirname(__file__), "..", "docs", "api"), "swagger-ui.html")


@app.get("/docs/openapi.yaml")
def swagger_spec():
    return send_from_directory(os.path.dirname(__file__), "openapi.yaml", mimetype="text/yaml")


@app.get("/api/admin/token-info")
@admin_required
def token_info():
    return jsonify(ok=True, expires_in=TOKEN_TTL)


@app.get("/api/admin/users")
@admin_required
def list_users():
    tipo = request.args.get("tipo", "").strip()
    busca = request.args.get("busca", "").strip()
    if tipo and tipo not in {"admin", "usuario"}:
        return jsonify(ok=False, message="Filtro tipo inválido."), 400
    clauses, params = [], []
    if tipo:
        clauses.append("tipo=%s")
        params.append(tipo)
    if busca:
        clauses.append("(nome LIKE %s OR email LIKE %s)")
        params.extend([f"%{busca}%", f"%{busca}%"])
    where = f"WHERE {' AND '.join(clauses)}" if clauses else ""
    try:
        with db_connection() as db, db.cursor() as cur:
            cur.execute(f"SELECT id,nome,email,telefone,provedor,tipo,data_criacao FROM usuarios {where} ORDER BY data_criacao DESC", params)
            return jsonify(ok=True, data=[serialize_user(row) for row in cur.fetchall()])
    except pymysql.MySQLError:
        return jsonify(ok=False, message="Não foi possível consultar as contas."), 503


@app.post("/api/admin/users")
@admin_required
def create_user():
    try:
        data = read_payload()
        values, error = validate_fields(data, creating=True)
        if error:
            return jsonify(ok=False, message=error), 400
        password_hash = php_password_hash(values.pop("senha"))
        params = [values["nome"], values["email"], values.get("telefone", ""), values["tipo"], password_hash, "local"]
        with db_connection() as db, db.cursor() as cur:
            cur.execute("INSERT INTO usuarios (nome,email,telefone,tipo,senha,provedor) VALUES (%s,%s,%s,%s,%s,%s)", params)
            user_id = cur.lastrowid
            db.commit()
            cur.execute("SELECT id,nome,email,telefone,provedor,tipo,data_criacao FROM usuarios WHERE id=%s", (user_id,))
            return jsonify(ok=True, data=serialize_user(cur.fetchone())), 201
    except pymysql.err.IntegrityError:
        return jsonify(ok=False, message="Este e-mail já está cadastrado."), 409
    except ValueError as error:
        return jsonify(ok=False, message=str(error)), 400
    except pymysql.MySQLError:
        return jsonify(ok=False, message="Não foi possível criar a conta."), 503


@app.route("/api/admin/users/<int:user_id>", methods=["PATCH", "PUT"])
@admin_required
def update_user(user_id: int):
    try:
        values, error = validate_fields(read_payload())
        if error:
            return jsonify(ok=False, message=error), 400
        if not values:
            return jsonify(ok=False, message="Informe ao menos um campo para alterar."), 400
        if "senha" in values:
            values["senha"] = php_password_hash(values["senha"])
        assignments = ", ".join(f"{field}=%s" for field in values)
        with db_connection() as db, db.cursor() as cur:
            cur.execute(f"UPDATE usuarios SET {assignments} WHERE id=%s", [*values.values(), user_id])
            if cur.rowcount == 0:
                cur.execute("SELECT id FROM usuarios WHERE id=%s", (user_id,))
                if not cur.fetchone():
                    return jsonify(ok=False, message="Conta não encontrada."), 404
            db.commit()
            cur.execute("SELECT id,nome,email,telefone,provedor,tipo,data_criacao FROM usuarios WHERE id=%s", (user_id,))
            return jsonify(ok=True, data=serialize_user(cur.fetchone()))
    except pymysql.err.IntegrityError:
        return jsonify(ok=False, message="Este e-mail já está cadastrado."), 409
    except ValueError as error:
        return jsonify(ok=False, message=str(error)), 400
    except pymysql.MySQLError:
        return jsonify(ok=False, message="Não foi possível atualizar a conta."), 503


@app.delete("/api/admin/users/<int:user_id>")
@admin_required
def delete_user(user_id: int):
    authorization = request.headers.get("Authorization", "")[7:].strip()
    current = verify_token(authorization)
    if current and int(current["sub"]) == user_id:
        return jsonify(ok=False, message="Você não pode excluir a própria conta de administrador."), 400
    try:
        with db_connection() as db, db.cursor() as cur:
            cur.execute("DELETE FROM usuarios WHERE id=%s", (user_id,))
            if cur.rowcount == 0:
                return jsonify(ok=False, message="Conta não encontrada."), 404
            db.commit()
            return jsonify(ok=True, message="Conta excluída com sucesso.")
    except pymysql.MySQLError:
        return jsonify(ok=False, message="Não foi possível excluir a conta."), 409


@app.errorhandler(HTTPException)
def http_error(error):
    return jsonify(ok=False, message=error.description), error.code


@app.errorhandler(Exception)
def unexpected_error(_error):
    app.logger.exception("Erro inesperado na API administrativa")
    return jsonify(ok=False, message="Erro interno na API."), 500


if __name__ == "__main__":
    app.run(host=os.getenv("HOST", "127.0.0.1"), port=int(os.getenv("ADMIN_API_PORT", "5001")), debug=os.getenv("DEBUG", "0") == "1")
