---
status: complete
date: 2026-08-19
slug: frete-gratis-sem-calculo
description: Ocultar o calculo de frete no detalhe do produto e carrinho quando o produto tiver frete gratis
---

# Quick Task Summary: Ocultar calculo de frete para produtos com frete gratis

## Mudanças Realizadas
- **PDP (Visualização)**: Ocultado o card `#pdp-frete-card` quando o produto possuir `frete_gratis` ativo.
- **Carrinho e Totais**: Adicionada verificação automática `temFreteGratis()` em `CarrinhoService`. O campo de digitação de CEP no carrinho é substituído por uma caixa de destaque de Frete Grátis.
- **Fechamento de Pedido**: `PedidoService` reconhece pedidos com frete grátis e registra modalidade sem custo.
