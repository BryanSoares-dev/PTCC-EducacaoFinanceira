# AFDE � resposta a incidentes

Este documento � uma minuta operacional e n�o substitui revis�o jur�dica.

## Conten��o

Suspender credenciais afetadas, preservar logs sem expor tokens, bloquear endpoints comprometidos e separar a c�pia afetada da produ��o. N�o executar limpeza destrutiva antes de preservar evid�ncias.

## Avalia��o

Identificar dados pessoais envolvidos, per�odo, usu�rios afetados, origem, vetor, integridade e se houve acesso, altera��o ou exfiltra��o. Registrar evid�ncias, hor�rios, respons�veis e decis�es.

## Comunica��o

Validar com o encarregado e advogado se o incidente pode acarretar risco ou dano relevante, os titulares que devem ser comunicados e os canais aplic�veis. Os prazos regulat�rios e o enquadramento do agente devem ser confirmados antes de qualquer comunica��o.

## Recupera��o

Rotacionar segredos, corrigir a causa-raiz, restaurar a partir de c�pia �ntegra, testar autentica��o/autoriza��es e monitorar o ambiente. Registrar a li��o aprendida e atualizar o mapa de seguran�a.

## Pend�ncias

Preencher contato do encarregado, respons�veis, contatos de emerg�ncia, pol�tica de reten��o de logs e crit�rios internos de severidade em `LEGAL-PENDENCIAS.md`.
