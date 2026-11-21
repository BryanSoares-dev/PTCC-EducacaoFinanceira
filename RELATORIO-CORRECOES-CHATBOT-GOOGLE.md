# Correções do chatbot e do Login com Google

## Problemas encontrados

- O widget chamava `http://127.0.0.1:5000/api/perguntar` diretamente no navegador. Em outro computador esse endereço aponta para a máquina do visitante; em HTTPS também pode ser bloqueado como mixed content. A chamada cross-origin ainda dependia de CORS.
- O serviço usava `gemini-3.1-flash-lite`, mas o projeto não trazia dependências nem configuração reproduzível para o serviço Python. A configuração lia apenas `GOOGLE_API_KEY` e criava o cliente mesmo sem chave.
- O Login com Google dependia de `back-end/google-config.php`, que é ignorado pelo projeto e não veio no pacote. Isso fazia a integração falhar quando o arquivo não existia.
- A sessão usava `SameSite=Strict`. O retorno OAuth ocorre por uma navegação iniciada em outro site, então o cookie pode não ser enviado e o `state` armazenado na sessão é perdido.
- Erros/cancelamentos do Google não eram exibidos de forma útil na tela de login.

## O que foi corrigido

- Criado `back-end/chatbot.php`, uma ponte same-origin com timeout, validação do JSON, limite de tamanho, resposta JSON consistente e encaminhamento controlado para `CHATBOT_API_URL`.
- O widget passou a chamar essa ponte relativa e a validar respostas antes de renderizar Markdown. A sanitização existente com DOMPurify foi mantida.
- O rate limit por IP continua ativo e recebe o IP do visitante somente pela ponte PHP.
- O serviço Python passou a aceitar `GEMINI_API_KEY` (com compatibilidade temporária para `GOOGLE_API_KEY`), modelo configurável e porta configurável. O padrão é `gemini-2.5-flash-lite`.
- Adicionados `suporte-ia/requirements.txt` e `suporte-ia/run.py` com Waitress para execução sem o servidor de desenvolvimento do Flask.
- O callback Google foi refeito com Authorization Code OAuth no servidor, `state` aleatório e comparação constante, troca HTTPS do código, UserInfo, tratamento de cancelamento, mensagens seguras e nenhuma dependência de `google-config.php`.
- A URI do callback e as credenciais agora vêm do `.env` por `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` e `GOOGLE_REDIRECT_URL`.
- A sessão usa `Lax` no desenvolvimento HTTP e `None; Secure` no HTTPS, compatível com retorno OAuth e Preview embutido.
- A tela de login exibe mensagens pendentes e aceita a mesma faixa de senha do backend.

## Configuração obrigatória

1. Copie `.env.example` para `.env`.
2. Preencha `GEMINI_API_KEY` e mantenha a chave somente no servidor.
3. Para Google, crie um OAuth Client do tipo Web application e cadastre exatamente a mesma `GOOGLE_REDIRECT_URL` no Google Cloud Console e no `.env`.
4. Instale `suporte-ia/requirements.txt` e execute `python suporte-ia/run.py` no Windows ou o equivalente Linux.
5. Garanta PHP com cURL habilitado, MySQL acessível e HTTPS em produção.

## Validação realizada neste ambiente

- Compilação Python de `suporte-ia/app_md.py` e `suporte-ia/run.py`: concluída.
- Auditoria estática do callback, ponte, referências antigas e ausência de `.env`/segredos no pacote: concluída.
- Não foi possível executar lint PHP, Apache, MySQL, Gemini real ou OAuth real porque o sandbox não possui PHP, Composer, banco configurado, dependências Python instaladas nem as credenciais do projeto. A validação final desses dois serviços deve ser feita no ambiente XAMPP/produção com as chaves reais.

## Referências oficiais consultadas

- [Google OAuth 2.0 para aplicações web no servidor](https://developers.google.com/identity/protocols/oauth2/web-server)
- [Verificação de identidade Google no servidor](https://developers.google.com/identity/gsi/web/guides/verify-google-id-token)
- [Gemini 2.5 Flash-Lite](https://ai.google.dev/gemini-api/docs/models/gemini-2.5-flash-lite)
- [OpenAI API — autenticação e chaves no servidor](https://developers.openai.com/api/reference/overview/)
