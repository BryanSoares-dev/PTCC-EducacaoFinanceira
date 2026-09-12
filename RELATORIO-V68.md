# AFDE v68 — Editor de foto de perfil

A versão 68 adiciona um editor de imagem no perfil para recorte quadrado 1:1. O usuário pode escolher uma nova imagem, arrastá-la dentro da área de recorte, ajustar zoom pelo controle deslizante ou pela roda do mouse e confirmar uma prévia circular antes do envio.

| Recurso | Implementação |
|---|---|
| Recorte 1:1 | Canvas quadrado de alta resolução com guia circular e grade auxiliar |
| Ajuste manual | Arraste por ponteiro, mouse ou toque |
| Zoom | Slider com faixa calculada para a imagem e roda do mouse |
| Prévia | Atualização em tempo real em formato circular |
| Confirmação | Canvas exportado para JPEG e enviado pelo formulário existente |
| Troca repetida | O input é limpo ao cancelar e recebe o novo arquivo no próximo fluxo |
| Visual | Modal Apple-like liquid glass, bordas arredondadas, blur e botões translúcidos |
| Responsividade | Layout adaptado para telas pequenas, com ações empilhadas |

O backend continua aceitando JPG, PNG, WEBP e GIF. Quando o usuário edita uma imagem GIF, o navegador exporta o recorte final como JPEG para garantir que a composição 1:1 seja preservada de forma consistente. GIFs enviados por fluxos sem recorte continuam aceitos pelo backend.

A versão mantém a proteção CSRF, validação MIME, limite de tamanho e substituição segura do arquivo anterior. Os scripts foram separados em `JS/perfil-crop.js`, enquanto o CSS continua centralizado em `css/app.css`.
