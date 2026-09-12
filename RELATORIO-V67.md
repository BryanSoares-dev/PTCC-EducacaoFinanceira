# AFDE v67 — Auditoria e aperfeiçoamento técnico

## Conclusão

A versão 67 consolida a camada de segurança e a base de manutenção do AFDE. O projeto agora centraliza a configuração de sessão, utiliza proteção CSRF nas principais mutações, preserva a conexão MySQL na porta 3307, endurece uploads de imagem, mantém avatar e banner com GIF, reduz carregamento de imagens fora da área inicial e elimina placeholders PNG inválidos.

## Correções principais

| Área | Problema identificado | Correção aplicada |
|---|---|---|
| Sessões | Cada página chamava `session_start()` com configurações implícitas | Criado `back-end/bootstrap.php` com modo estrito, cookies HttpOnly/SameSite e regeneração no login |
| CSRF | Formulários de alteração não tinham token | Criados tokens por sessão e validação em login, cadastro, movimentações, perfil, senha, preferências e uploads |
| Banco | Mensagem de conexão expunha erro interno | PDO com prepared statements, `utf8mb4`, timeout, fetch associativo e log privado |
| Uploads | Validação limitada e sem proteção uniforme | MIME real, `getimagesize`, allowlist, limite por tipo, nome aleatório e usuário autenticado |
| Perfil | Foto sem banner ou com caminhos frágeis | Avatar e banner independentes, GIF permitido, feedback de erro/sucesso e limpeza do arquivo anterior |
| Assets | PNGs inválidos eram código salvo com extensão de imagem | Removidos placeholders inválidos e mantidos SVGs válidos da identidade AFDE |
| Performance | Imagens não críticas carregavam imediatamente | `loading="lazy"` e `decoding="async"` onde aplicável |
| Headers | Navegador não recebia políticas básicas | `nosniff`, `SAMEORIGIN`, `Referrer-Policy` e `Permissions-Policy` |

## Migração obrigatória

Instalações existentes precisam executar `migrations/001_add_banner.sql` uma vez para criar a coluna `usuarios.banner`. Instalações novas podem importar o `educacaofinanceira.sql` atualizado.

## Limites e validação

A foto de perfil aceita JPG, PNG, WEBP e GIF até 5 MB. O banner aceita os mesmos formatos até 8 MB. A extensão informada pelo navegador não é usada como fonte única de confiança. Os arquivos recebem nomes gerados pelo servidor.

## Validação executada

Os 52 arquivos PHP foram verificados com `php -l` e não apresentaram erros de sintaxe. Os scripts JavaScript foram verificados com `node --check`. As referências antigas aos PNGs inválidos foram removidas. O projeto mantém um único CSS universal. O pacote ZIP foi testado com `unzip -tq`. A validação funcional com banco de dados deve ser executada no XAMPP, WAMP, Laragon ou servidor PHP local conectado ao MySQL na porta 3307.

## Referências

As decisões de upload seguem a orientação de defesa em profundidade da OWASP [1]. As configurações de sessão seguem a documentação oficial do PHP [2]. A otimização de imagens segue a recomendação de lazy loading da MDN [3].

[1]: https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html "OWASP File Upload Cheat Sheet"
[2]: https://www.php.net/manual/en/session.security.ini.php "PHP Manual — Securing Session INI Settings"
[3]: https://developer.mozilla.org/en-US/docs/Web/Performance/Guides/Lazy_loading "MDN — Lazy loading"
