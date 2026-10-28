# AFDE � backlog

- BL-001 | frente B | P0 | raiz/htaccess, artefatos | `htaccess` estava sem ponto e inerte; artefatos `files.zip`, `inject.py` e `__pycache__` existem na c�pia | ativar `.htaccess`, bloquear extens�es e retirar artefatos de publica��o em entrega final | parcialmente fechado na c�pia; limpeza f�sica ainda pendente
- BL-002 | frente B | P0 | projeto inteiro | 79 PHP, 217 `!important`, 174 ocorr�ncias de `backdrop-filter`, 57 `innerHTML/insertAdjacentHTML` | mapear por endpoint/tela antes de corrigir; n�o fazer refatora��o ampla sem gates | aberto
- BL-003 | frente B | P1 | seguran�a/headers | prote��o de headers aparece centralizada, mas cobertura efetiva ainda n�o foi testada com Apache | verificar est�tica e com servidor local quando dispon�vel | aberto
- BL-004 | frente C | P1 | privacidade.php/exportar-dados.php | refer�ncia a endpoint de exporta��o n�o encontrado no diagn�stico anterior | implementar ap�s mapear sess�o, CSRF e formato CSV | aberto
- BL-005 | frente C | P1 | termos/aceites | aceite versionado e prova de aceite ainda precisam ser confirmados | criar migration aditiva somente em c�pia e validar sem banco real | aberto
- BL-006 | frente A | P2 | css/liquid-glass.css e JS/liquid-glass.js | excesso de efeitos e falta de reduced-transparency | reduzir somente ap�s invent�rio de telas e regress�o | aberto
- BL-007 | frente A | P1 | comunidade, investimentos, Arena, aprendizado | p�ginas usavam paletas independentes | camada final de tokens AFDE preservando componentes e fluxos | fechado estaticamente
- BL-008 | frente A/B | P1 | JS/arena-app.js: renderState | refer�ncia a `#arenaAvatar` inexistente interrompia a atualiza��o da tela | tornar refer�ncia opcional e atualizar XP com valor limitado + ARIA | fechado com `node --check`; teste visual autenticado ainda pendente
