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
