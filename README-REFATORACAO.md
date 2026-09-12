# Educação Financeira — refatoração visual e estrutural

## O que foi corrigido

- Todas as folhas de estilo específicas foram consolidadas em `css/app.css`.
- Foram removidos os blocos `<style>` internos de todas as páginas HTML/PHP, incluindo videoaulas, exercícios, aprendizado, loja, diagnóstico e teste da Brapi.
- As páginas foram normalizadas para carregar o CSS universal com caminho relativo correto.
- O design system recebeu tema escuro consistente em azul-marinho e verde, efeito liquid glass, bordas translúcidas, blur, sombras em camadas e estados de foco acessíveis.
- Foram adicionadas animações fluidas de entrada, hover, elevação, foco e movimento ambiental dos elementos decorativos.
- O CSS inclui responsividade para telas menores e respeito a `prefers-reduced-motion`.

## Validação executada

- `css/` contém somente `app.css`.
- Nenhuma página `.php` ou `.html` contém a tag `<style>`.
- As 47 referências encontradas apontam para `app.css`.
- Os arquivos JavaScript existentes passaram em `node --check`.

## Observação sobre o ambiente

A exportação recebida foi um arquivo Markdown do ZIP original. O projeto foi reconstruído a partir dos blocos de código presentes nele. O ambiente de execução desta sessão não possui o binário PHP instalado; por isso, não foi possível executar `php -l` localmente. A lógica de backend foi preservada, sem alterações funcionais fora da camada de apresentação.

## Atualização para celulares

A camada mobile-first foi adicionada ao final de `css/app.css`. Os layouts agora se adaptam progressivamente em 900 px, 680 px e 380 px. Grids de cards, formulários, produtos, aulas, estatísticas e dashboards passam a uma coluna em telas estreitas; botões e campos recebem áreas de toque de pelo menos 44 px; inputs usam fonte de 16 px para evitar zoom automático no iOS; tabelas ficam contidas em wrappers com rolagem horizontal; modais respeitam a altura dinâmica do navegador; e navegações e barras laterais passam a rolar horizontalmente sem estourar a viewport.

Também foram adicionadas metas `viewport` nas páginas renderizadas que ainda não tinham essa configuração. A atualização mantém o CSS universal único, a identidade liquid glass e o suporte a redução de movimento.

## Auditoria técnica e versão melhorada

A auditoria encontrou problemas funcionais importantes. O endpoint `item.php` carregava `conexao.php` a partir de um caminho inexistente, não verificava autenticação, aceitava qualquer método HTTP e acessava chaves JSON sem validação. A página de cadastro não carregava o CSS universal. A conexão usava credenciais fixas no código, não declarava `utf8mb4` na DSN e mostrava a mensagem interna do banco diretamente ao usuário. O login não regenerava o ID da sessão após autenticação. Cadastro e movimentações aceitavam entradas sem validação suficiente. O upload de avatar usava caminhos relativos dependentes do diretório de execução, validava apenas a extensão informada pelo cliente e podia deixar mensagens internas expostas. A página de configurações ainda duplicava o prefixo do caminho de avatar.

Esses pontos foram corrigidos na versão atual. A conexão agora suporta variáveis `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`, usa prepared statements reais, `utf8mb4` e mensagens públicas seguras. Login, cadastro, movimentações e atualização de item passaram a validar método, autenticação, formato e tamanho dos dados. O login regenera a sessão. O upload usa MIME detectado no conteúdo, nome aleatório, diretório absoluto do servidor e bloqueio de execução de scripts em `uploads/.htaccess`. O cadastro voltou a carregar `css/app.css`, e os caminhos de avatar foram padronizados.

A validação de JavaScript continua passando com `node --check`, não existem blocos `<style>` internos e permanece apenas uma folha CSS universal. A validação `php -l` não pôde ser executada porque o binário PHP não está instalado neste ambiente; por isso, os arquivos PHP corrigidos foram revisados estaticamente e mantidos com sintaxe compatível com PHP 8.

## Correção do erro de conexão com o banco

A mensagem aparecia porque todas as páginas dependem de `back-end/conexao.php` e a conexão falha quando o MySQL/MariaDB está desligado, quando o banco `educacaofinanceira` ainda não foi criado ou quando o usuário e a senha locais são diferentes dos padrões. A conexão foi ajustada para usar `localhost`, aceitar `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`, manter `utf8mb4` e registrar o detalhe técnico apenas no log do servidor.

Para executar localmente, primeiro inicie o módulo **MySQL** no XAMPP/WAMP/Laragon. Depois importe `educacaofinanceira.sql` no phpMyAdmin ou pelo terminal. O SQL atualizado já executa `CREATE DATABASE IF NOT EXISTS educacaofinanceira` e `USE educacaofinanceira`, então pode ser importado sem criar o banco manualmente. Se a instalação local usa senha no usuário `root`, defina `DB_PASS` com essa senha; se usa outra porta, defina `DB_PORT`.

Exemplo pelo terminal:

```bash
mysql -u root -p < educacaofinanceira.sql
```

Se o usuário local não possui senha, use `mysql -u root < educacaofinanceira.sql`. O arquivo `.env.example` mostra os nomes das configurações esperadas. O PHP precisa ter as extensões `pdo` e `pdo_mysql` habilitadas.

## Configuração local definida para MySQL na porta 3307

Nesta versão, `back-end/conexao.php` está configurado diretamente para `127.0.0.1`, porta `3307`, banco `educacaofinanceira`, usuário `root` e senha vazia, conforme solicitado. O MySQL precisa estar iniciado nessa porta e o banco precisa ter sido importado pelo arquivo `educacaofinanceira.sql`.

## Correção visual e assets quebrados

A exportação Markdown original continha arquivos com extensão PNG que, na prática, eram trechos de código inválidos. Isso explicava o ícone de imagem quebrada e parte do visual branco/ilegível da captura. Foram criados assets SVG válidos para logo, favicon, avatar padrão, fundo e cards de investimentos, mantendo a paleta azul-marinho e verde da versão original. As referências de páginas foram atualizadas para SVG e os cards de investimentos agora não dependem de arquivos inexistentes.

Também foi adicionada uma camada CSS final de compatibilidade para neutralizar regras legadas conflitantes. Ela restaura fundo escuro, texto claro, cards glass, campos escuros, botões verdes, tabelas legíveis e layouts de cadastro/login sem modificar a estrutura funcional das páginas.

## Identidade AFDE e correção do footer lateral

O nome visual foi restaurado para **AFDE** em títulos, textos, alt texts e logo. A barra lateral aparecia porque a regra global do painel administrativo (`body { display: flex; }`) havia sido concatenada no CSS universal e vazava para as páginas públicas. As páginas administrativas agora recebem `body.admin-page`, enquanto páginas públicas retornam ao fluxo normal de bloco; o footer passa a ocupar toda a largura, com painel liquid glass, bordas arredondadas, blur e espaçamento inspirado no acabamento Apple.

Também foi criada uma camada final de compatibilidade para preservar os componentes originais, arredondar cards, aplicar ícones verdes translúcidos e manter a paleta azul-marinho/verde. Como os arquivos de imagem originais da exportação estavam corrompidos e não eram PNGs válidos, o pacote contém assets SVG válidos em estilo AFDE para logo, favicon, avatar, fundo e ícones de investimento.

## Avatar e banner de perfil

O perfil agora possui dois uploads independentes: **foto de perfil** e **banner**. Ambos aceitam JPG, PNG, WEBP e GIF; a foto possui limite de 5 MB e o banner de 8 MB. O servidor valida o MIME real, a leitura da imagem, o tamanho, o upload HTTP e gera nomes aleatórios, evitando confiar na extensão enviada pelo navegador. Os arquivos antigos do mesmo campo são removidos após a atualização bem-sucedida.

A página de perfil exibe o banner em um cabeçalho liquid glass responsivo e mantém o avatar circular. Para bancos criados antes desta atualização, execute uma vez `migrations/001_add_banner.sql` no phpMyAdmin ou no terminal. O `educacaofinanceira.sql` de instalações novas já inclui a coluna `banner`.

```bash
mysql -u root -p -P 3307 educacaofinanceira < migrations/001_add_banner.sql
```
