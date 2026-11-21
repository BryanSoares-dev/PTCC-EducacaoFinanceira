# Auditoria de profissionalismo — AFDE

## Resumo

O projeto tem bastante funcionalidade, mas ainda mistura PHP server-rendered, Flask, Node/Express e múltiplos serviços Python sem uma camada única de configuração, deploy e observabilidade. Isso não significa que o site não funcione; significa que a manutenção, a segurança e a previsibilidade de produção ficam mais difíceis.

## Correções aplicadas nesta rodada

- Criado `.htaccess` ativo; o arquivo anterior se chamava `htaccess` e não era reconhecido automaticamente pelo Apache.
- Bloqueados no Apache arquivos `.env`, dumps SQL, logs, backups, ZIPs, configurações de dependências e diretórios de controle de versão.
- Adicionados headers básicos de segurança e desativada a listagem de diretórios.
- Unificadas as duas conexões PDO: `back-end/conexao.php` agora usa a conexão central de `/conexao.php`, eliminando divergências de porta e defaults.
- Adicionados uploads, temporários e vídeos ao `.gitignore` para evitar que dados de usuários entrem em novos pacotes/commits.
- A API administrativa Flask já usa Waitress, valida segredo forte em produção, rate limit, CORS restrito, headers de segurança, timeouts MySQL e Swagger CRUD dedicado.

## Problemas prioritários ainda existentes

### P0 — antes de publicar

1. **Serviços Flask legados ainda usam `app.run()`** em `analise-de-extrato/app.py` e `suporte-ia/app_md.py`. Eles devem usar Waitress/Gunicorn, assim como a API administrativa.
2. **A API de análise de extrato não está ligada à sessão PHP.** A página exige login, mas o endpoint Flask aceita requisições independentes; o rate limit por IP reduz abuso, mas não substitui autenticação, autorização e controle de cota.
3. **Há endpoints e portas hardcoded no frontend**, como `127.0.0.1:5000` e `localhost:3000`. Isso quebra em outra máquina, HTTPS, proxy reverso ou produção.
4. **Há alterações de schema durante requisições web**, em `navbar.php`, `perfil.php`, `configuracoes.php`, `teste_diagnostico.php`, `pular_teste.php` e outros. Migrações devem ser versionadas e executadas separadamente.
5. **O pacote contém artefatos de desenvolvimento e distribuição**, incluindo executável de Swagger, `files.zip`, `inject.py` e mídia de upload. Eles não devem ser publicados no document root nem entrar no artefato final.
6. **Não há teste automatizado completo** para login, permissões admin, CSRF, uploads, CRUD, Open Finance, pagamentos e comunidade. Os relatórios existentes reconhecem que a revisão ainda é estática.

### P1 — qualidade e manutenção

7. **Arquitetura duplicada:** regras de autenticação, banco, respostas e configuração são distribuídas em muitos arquivos PHP grandes, mais três serviços Python e um serviço Node.
8. **Configuração de ambiente fragmentada:** cada serviço lê variáveis e defaults de forma própria. O ideal é um `.env.example` por ambiente e uma tabela central de portas/URLs.
9. **Schema sem migrações formais:** criar tabela/coluna no primeiro acesso causa lentidão, condições de corrida e falha silenciosa.
10. **Observabilidade insuficiente:** faltam logs estruturados, request ID, métricas, health checks de dependências e política de retenção de logs.
11. **Dependências sem uma rotina de auditoria no CI:** executar `composer audit`, `npm audit` e scanner de dependências em cada mudança deve ser obrigatório.
12. **Páginas e scripts muito grandes:** o JavaScript da comunidade concentra muitas responsabilidades e usa bastante HTML dinâmico. Embora várias saídas sejam escapadas, isso aumenta o risco de regressões de XSS.

### P2 — acabamento percebido pelo usuário

13. **Excesso de efeitos visuais:** o relatório anterior registrou 217 usos de `!important` e 174 de `backdrop-filter`. Isso tende a parecer inconsistente, prejudicar performance e dificultar acessibilidade.
14. **Documentação com histórico misturado ao código:** `_loop/` registra decisões úteis, mas há sinais de encoding inconsistente em alguns arquivos de histórico; documentação publicada deve ser UTF-8 validada.
15. **Privacidade e LGPD incompletas:** o relatório anterior registra pendências de exportação, exclusão/anonimização, aceite versionado e revisão jurídica.

## Ordem recomendada para a próxima etapa

1. Autenticar/proteger o analisador de extratos e centralizar as URLs dos serviços.
2. Criar migrações SQL versionadas e remover `ALTER TABLE` do carregamento das páginas.
3. Separar document root público de uploads, temporários, backups e código-fonte.
4. Criar smoke tests automatizados para login, admin CRUD, CSRF e endpoints JSON.
5. Colocar os serviços atrás de um proxy reverso HTTPS com logs e health checks.
6. Só depois fazer a limpeza visual incremental, com teste de contraste e regressão.

## Limite desta auditoria

A análise foi estática e executou apenas testes locais da API administrativa. Não foi feita validação contra um banco real, Apache/XAMPP real, navegador autenticado, produção, Pluggy, Asaas ou Google. Portanto, o projeto ainda não deve ser considerado pronto para produção sem essa etapa.
