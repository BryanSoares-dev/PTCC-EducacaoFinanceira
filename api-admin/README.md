# API administrativa Flask

A API gerencia contas da tabela `usuarios` do mesmo banco usado pelo PHP.

## Instalação e execução

Na raiz do projeto:

```bash
python -m venv .venv
.venv\\Scripts\\activate       # Windows
# source .venv/bin/activate     # Linux/macOS
pip install -r api-admin/requirements.txt
python api-admin/run.py
```

A API inicia em `http://127.0.0.1:5001`. A tela `admin/usuarios.php` lê `ADMIN_API_URL` do `.env` (ou usa esse endereço padrão).

`run.py` usa Waitress, um servidor WSGI adequado para desenvolvimento local e produção no Windows. Em Linux/macOS, o equivalente é:

```bash
gunicorn --chdir api-admin --bind 127.0.0.1:5001 --workers 2 --threads 4 --timeout 30 wsgi:application
```

Defina no `.env` um `AFDE_ADMIN_API_SECRET` forte. O PHP emite um token HMAC de 15 minutos somente para o administrador logado; a API valida o token e confirma a permissão diretamente no banco.

## Swagger

Com a API em execução, abra `http://127.0.0.1:5001/docs`. O arquivo OpenAPI também foi adicionado ao Swagger já existente em `docs/api/openapi.yaml` para leitura no XAMPP.

O grupo **Contas administrativas** mostra explicitamente as quatro operações CRUD: `GET /api/admin/users` (listar), `POST /api/admin/users` (criar), `PUT /api/admin/users/{userId}` (atualizar) e `DELETE /api/admin/users/{userId}` (excluir).
