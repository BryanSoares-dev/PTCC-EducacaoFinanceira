# API RESTful de administradores

A API implementa as quatro operações do CRUD solicitado:

- `GET /api/admins` — lista perfis administrativos;
- `POST /api/admins` — cria um perfil `admin`;
- `PUT /api/admins/{id}` — edita nome, e-mail, telefone e, opcionalmente, senha;
- `DELETE /api/admins/{id}` — exclui um perfil, sem permitir apagar a própria conta ou o último administrador.

Ela também implementa o mesmo CRUD para usuários comuns, sempre mantendo `tipo='usuario'`:

- `GET /api/users`;
- `POST /api/users`;
- `PUT /api/users/{id}`;
- `DELETE /api/users/{id}`.

Essas rotas nunca listam, editam ou excluem administradores.

## Execução no Windows/XAMPP

1. Copie `.env.example` para `.env` e defina `ADMIN_API_SECRET` com um segredo longo e aleatório.
2. Crie o ambiente: `py -m venv api/venv`.
3. Instale as dependências: `api/venv/Scripts/pip install -r api/requirements.txt`.
4. Inicie `api/iniciar-admin-api.bat`.
5. Inicie o `AFDE-Swagger-Launcher.exe` para abrir o Swagger.

O painel `admin/administradores.php` emite tokens HMAC curtos para a sessão admin atual. A API não armazena nem retorna senhas.

## Swagger

A documentação está em `docs/api/openapi.yaml` e é carregada pelo `docs/api/swagger-ui.html`. Como o launcher é um executável que apenas inicia o XAMPP e abre essa página, não é necessário recompilar o binário para atualizar as rotas: o Swagger passa a mostrar as quatro operações assim que o YAML é atualizado.
