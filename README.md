# PTCC-EducacaoFinanceira

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
