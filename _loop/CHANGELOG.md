# AFDE � changelog

- Itera��o 0: criada a estrutura `_loop/`, registrado diagn�stico inicial, definida a identidade AFDE (`#0A2540` + `#16E28A`) e ativado `.htaccess` somente na c�pia de trabalho.
- Itera��o 0: Git n�o est� instalado; n�o houve commit. PHP/MySQL/Chrome headless n�o foram usados para validar a c�pia; achados permanecem como verifica��o est�tica.
- Itera��o 1: unificada a identidade visual AFDE da comunidade, investimentos, Arena e �rea educacional com `#0A2540` e `#16E28A`, mantendo os fluxos existentes.
- Itera��o 1: corrigido o erro de `#arenaAvatar` inexistente que interrompia `renderState`; a barra de XP agora usa valor num�rico limitado de 0�100, `aria-valuenow` e atualiza��o visual protegida.
- Itera��o 1: cache-busting atualizado nas p�ginas afetadas; `php -l` e `node --check` passaram ap�s normaliza��o de UTF-8 sem BOM.
- Iteração 2: corrigida a codificação dos textos visíveis e símbolos, incluindo a comunidade; adicionado `AddDefaultCharset UTF-8` ao `.htaccess` da cópia.
- Iteração 2: adicionadas superfícies Liquid Glass funcionais à comunidade, investimentos e diagnóstico, com fallback `prefers-reduced-transparency` e cache-busting v103.
