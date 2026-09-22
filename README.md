# PTCC-EducacaoFinanceira

## Configuração do MySQL no XAMPP

O projeto está configurado para conectar ao MySQL/MariaDB do XAMPP pela porta padrão **3306**. Verifique no arquivo `xampp/mysql/bin/my.ini` se a diretiva `port` está definida como `3306`, inicie o MySQL e importe `educacaofinanceira.sql` no banco `educacaofinanceira`. Se o usuário `root` possuir senha, atualize a variável `$pass` no arquivo `back-end/conexao.php`.

## Acessibilidade

O widget de acessibilidade (`JS/acessibilidade.js`) está integrado em todas as páginas do site
(`front-end/*.php` e `admin/*.php`), sendo carregado antes do fechamento de `</body>`.

Funcionalidades do widget:
- Ajuste de tamanho de fonte (diminuir, padrão, aumentar)
- Filtros de simulação de daltonismo (protanopia, deuteranopia, tritanopia, acromatopsia)
- Modo de alto contraste
- Preferências salvas no `localStorage` do navegador
- Painel acessível via teclado (Esc fecha, foco automático) e leitor de tela (ARIA)



## Calendário financeiro integrado ao Open Finance

A carteira agora sincroniza as transações da conta conectada pela Pluggy e salva os lançamentos em `open_finance_transacoes`. O calendário combina esses registros com as movimentações manuais, mostra o mês atual do primeiro dia até o dia corrente e oferece navegação pelos meses desde a criação da conta. O gráfico histórico compara receitas e despesas mês a mês.

Antes do primeiro uso, execute `open-finance/migration.sql` no banco `educacaofinanceira` ou use o dump atualizado `educacaofinanceira.sql`. Configure `PLUGGY_CLIENT_ID` e `PLUGGY_CLIENT_SECRET` como variáveis de ambiente do PHP. O botão de conexão mantém `includeSandbox: true` para permitir contas de teste durante o desenvolvimento; em produção, essa opção deve ser desativada quando não houver necessidade de conectores sandbox.

A categorização automática usa a categoria retornada pela Pluggy e regras simples baseadas na descrição, operação e comerciante. Na carteira, cada transação importada possui um seletor para o usuário corrigir a categoria; essa escolha fica marcada como manual e é preservada nas sincronizações seguintes. As categorias disponíveis incluem **Alimentação**, **Transporte**, **Moradia**, **Contas**, **Saúde**, **Educação**, **Lazer** e **Outros**.

A sincronização é acionada ao abrir ou atualizar os dados da carteira. A integração usa o endpoint cursor-based `/v2/transactions` com páginas de até 500 registros e mantém fallback para o endpoint legado. A Pluggy disponibiliza normalmente até 12 meses de histórico por produto de transações; o gráfico mostra todos os meses existentes no cadastro e, para meses sem lançamentos, exibe zero.

## Perfil, foto e banner

A página `front-end/perfil.php` permite editar os dados pessoais, trocar a foto com um editor em formato **1:1**, aplicar zoom e reposicionar a imagem arrastando-a, além de enviar um banner para o cabeçalho do perfil. Bancos criados com uma versão anterior recebem a coluna `banner` automaticamente ao abrir o perfil; alternativamente, execute `migration_perfil.sql` uma única vez no banco `educacaofinanceira`.

## Uploads no XAMPP

Os uploads são gravados em `uploads/perfis` e `uploads/banners`. Essas pastas precisam existir e ter permissão de escrita para o usuário do Apache. O banner é enviado automaticamente assim que um arquivo é escolhido; a foto de perfil é salva como JPEG quadrado após o recorte no pop-up e não depende da extensão GD.

## Editor de mídia — versão 67

O editor de foto é aberto pelo avatar, pelo botão **Alterar foto** ou por teclado. Ele produz um JPEG quadrado com zoom e arraste. O editor de banner produz um JPEG horizontal 3:1 com os mesmos controles. O arquivo `VERSOES-PERFIL.md` cataloga as 67 evoluções do fluxo.

### Evolução 68 do editor

O zoom mínimo do avatar e do banner é 50%, com preenchimento derivado da própria imagem para não revelar áreas vazias. O arraste é limitado às bordas do conteúdo, e as saídas foram ampliadas para 1024×1024 no avatar e 1800×600 no banner, com maior qualidade de interpolação e compressão JPEG.

## Liquid Glass v83 — rodada de 15 evoluções

Todas as 28 páginas visuais do site carregam `css/liquid-glass.css?v=83` e `JS/liquid-glass.js?v=83`. A camada inclui superfícies translúcidas, blur, saturação, borda especular, brilho que acompanha o ponteiro, tilt suave, ripple aquático no clique, estados de foco, tratamento responsivo e fallback para redução de movimento. O catálogo completo está em `VERSOES-LIQUID-GLASS.md`.

## Liquid Glass v98 — segunda rodada de 15 evoluções

A rodada v84-v98 adiciona transições suaves entre páginas, reveal por visibilidade, controles magnéticos, indicação da rota atual, blobs e corrente atmosférica animados, halo de foco, resposta fluida de tabelas e gates para touch e redução de movimento. As 30 mudanças acumuladas estão catalogadas em `VERSOES-LIQUID-GLASS.md`.

## Liquid Glass v99 — acabamento suave e parallax

A revisão v99 uniformiza o material em todas as páginas, reduz a intensidade das luzes brancas, neutraliza fundos conflitantes dos itens da navbar, reduz o tilt, adiciona um parallax atmosférico amortecido e uma pequena bolinha de luz que acompanha o mouse. O orbe é não-interativo e é desativado em touch e `prefers-reduced-motion`.


## Configuração atual do banco

A conexão ativa do projeto usa `127.0.0.1:3306`. Se o XAMPP estiver usando outra porta, altere `$port` em `back-end/conexao.php` e a diretiva correspondente no `my.ini`.
