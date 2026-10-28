# AFDE � mapa inicial de seguran�a

Status inicial: **verificado estaticamente**. N�o foi executado contra banco real nem produ��o.

| �rea | M�todo | Sess�o/papel | CSRF | Propriedade/IDOR | Entrada/sa�da | Rate limit | Status |
|---|---|---|---|---|---|---|---|
| `back-end/*` | GET/POST | sess�o varia por endpoint | parcial | mapear por endpoint | prepared statements/escape variam | parcial | FALHA a mapear |
| `open-finance/*` | GET/POST | sess�o de usu�rio | POST exige confirma��o em v�rios pontos | item/transa��o precisam revis�o | Pluggy + JSON | n�o confirmado | FALHA a mapear |
| `community/back-end/api.php` | POST | sess�o de usu�rio | helper de comunidade | a��es e IDs precisam revis�o | JSON e uploads | parcial | FALHA a mapear |
| `admin/*` | GET/POST | admin_auth.php precisa cobertura | revisar por a��o | IDs administrativos | PHP/HTML | n�o confirmado | FALHA a mapear |
| `asaas-pagamento/*` | POST/webhook | sess�o/webhook | webhook por token | idempot�ncia/valor precisam revis�o | JSON/cURL | n�o confirmado | FALHA a mapear |
| `analise-de-extrato/app.py` | POST | sem login confirmado | N/A | arquivo � do chamador | PDF/CSV e tempor�rios | h� rate limit | PARCIAL |
| `JS/server-brapi.js` | GET | servi�o local | N/A | ticker precisa allowlist | CORS allowlist | n�o confirmado | PARCIAL |

## Pr�ximo passo

Inventariar cada endpoint com m�todo, autentica��o, papel, CSRF, propriedade, valida��o e resposta antes de corrigir regras de neg�cio.
