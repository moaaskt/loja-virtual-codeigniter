---
status: complete
date: 2026-08-19
slug: frete-gratis-sem-calculo
description: Ocultar o calculo de frete no detalhe do produto e carrinho quando o produto tiver frete gratis
---

# Quick Task: Ocultar calculo de frete para produtos com frete gratis

## Objetivo
Para qualquer produto cadastrado com frete gratis ativo (`frete_gratis = 1`), nao deve ser exibido o bloco "Calcular Frete" tanto na visualizacao do produto (PDP) quanto no carrinho de compras / fluxo de compra.

## Alteracoes Realizadas
1. **app/Views/shop/produto_detalhe.php**: Ocultacao do card de simulacao de frete quando `frete_gratis` estiver habilitado no produto.
2. **app/Services/CarrinhoService.php**: Adicao do metodo `temFreteGratis()` e integracao em `calcularTotais()` para atribuir automaticamente modalidade 'Frete Gratis' e valor 0.
3. **app/Views/shop/carrinho.php**: Substituicao do formulario de calculo de frete por um card informativo de "Frete Gratis Aplicado" quando os itens possuirem frete gratis.
4. **app/Services/PedidoService.php**: Gravacao automatica de `frete_modalidade = 'Frete Gratis'` e `frete_valor = 0.00` na criacao do pedido quando os itens forem frete gratis.
