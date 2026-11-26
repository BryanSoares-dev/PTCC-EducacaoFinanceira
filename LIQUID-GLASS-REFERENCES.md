# Referências da manutenção visual

A manutenção visual foi orientada pelas seguintes fontes consultadas em 26 de novembro de 2026:

- [Liquid Glass Design — What is Liquid Glass?](https://liquidglassdesign.com/what-is-liquid-glass)
- [Liquid Glass Design — Real liquid glass for the web](https://liquidglassdesign.com/resources/real-liquid-glass-for-the-web)
- [Liquid Glass Design — galeria e referências](https://liquidglassdesign.com/)
- Guia enviado pelo usuário com orientações da Apple sobre adoção do Liquid Glass.

## Decisões aplicadas

- Liquid Glass foi tratado como uma camada funcional para navegação, controles, cartões e modais, sem transformar todo o conteúdo em vidro.
- O material usa tint translúcido, blur/saturação, borda especular, brilho interno e sombras em camadas.
- A paleta de marca foi centralizada em `#16E28A` como cor de ação e `#1F2937` como base estrutural.
- Educação e Comunidade receberam tokens e superfícies finais compatíveis com o tema global.
- Foram incluídos fallback para `prefers-reduced-transparency` na Comunidade e preservados os gates de `prefers-reduced-motion` existentes.
