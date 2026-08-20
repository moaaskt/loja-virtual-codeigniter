---
slug: ajustes-layout-pdp
date: 2026-08-20
status: complete
description: Ajuste da estrutura Bootstrap e CSS da página de detalhes do produto (PDP) para telas Desktop
---

# Summary: Ajustes de Layout e CSS da PDP

## O que foi realizado:
1. **Estrutura Geral da PDP**:
   - Todo o conteúdo envolvido em `<div class="container py-4">`.
   - Topo dividido em `col-12 col-lg-7` (galeria com imagem principal e miniaturas) e `col-12 col-lg-5` (título, preço, variações, botão de compra, badges de confiança e simulador de frete).
2. **Reorganização de `#secao-avaliacoes`**:
   - Linha própria `<div class="row g-4 mt-2">` com largura total.
   - Coluna da esquerda (`col-12 col-lg-4`): Card "Resumo da Reputação" com nota geral, estrelas, distribuição percentual e classe `sticky-lg-top`.
   - Coluna da direita (`col-12 col-lg-8`): Topo com card de envio de avaliação (ou convite se deslogado) e abaixo o feed de comentários aprovados com badge "Compra Verificada".
3. **Recomendações ("Você também pode gostar")**:
   - Grade ajustada para `<div class="row row-cols-2 row-cols-md-4 g-3">` com cards `h-100` e altura uniforme.
4. **CSS (shop.css)**:
   - Aplicação de `border-radius: 12px`, sombras suaves `box-shadow: 0 2px 8px rgba(0,0,0,0.05)`, paddings ajustados e media queries para telas desktop (`@media (min-width: 992px)`).
5. **Validação**:
   - 55 testes PHPUnit passando com 100% de sucesso.
