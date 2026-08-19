# Quick Task: Variações e SKUs Genéricos e Flexíveis

## Objetivo
Tornar o sistema de variações/SKUs de produtos genérico e flexível para qualquer tipo de produto (Eletrônicos, Roupas, Calçados, Eletrodomésticos, etc.):
1. Inspecionar e atualizar a base de dados (`produto_variacoes`) para permitir variações livres (ex: "128GB", "256GB", "110V", "41", "M"), cor opcional e preço customizado por variação.
2. Refatorar o painel administrativo (cadastro e edição de produtos) com campos dinâmicos e livres de Variação/Atributo, Cor opcional, Preço individual por variação e Estoque.
3. Refatorar a vitrine e detalhes do produto no frontend com rótulos dinâmicos inteligentes (ex: "Capacidade / Modelo", "Voltagem", "Tamanho", "Variação"), atualização de preço em tempo real conforme a variação selecionada e suporte a variações com ou sem cor.
4. Garantir persistência e exibição correta no carrinho, checkout, resumo do pedido e painel administrativo.

## Arquivos Afetados
- `app/Database/Migrations/2026-08-19-224500_UpdateProdutoVariacoesGenericas.php` (nova migration)
- `banco_loja.sql` (atualização do schema)
- `app/Controllers/Admin/ProdutosController.php` (processamento de variações livres, preço customizado e cor opcional)
- `app/Views/admin/produtos/new.php` (interface de cadastro com variação livre, cor opcional e preço de SKU)
- `app/Views/admin/produtos/edit.php` (interface de edição com variação livre, cor opcional e preço de SKU)
- `app/Views/shop/produto_detalhe.php` (rótulos dinâmicos, atualização de preço em tempo real e seleção flexível)
- `app/Services/CarrinhoService.php` (suporte a preço por variação e mensagens genéricas)
- `app/Services/PedidoService.php` (cálculo de preço unitário por variação e baixa de estoque)
- `app/Views/shop/carrinho.php`, `app/Views/shop/checkout.php`, `app/Views/shop/pedido_pagamento.php`, `app/Views/cliente/meus_pedidos.php`, `app/Views/admin/pedidos/detalhe.php` (exibição de variações)
- `tests/app/VariacoesGenericasTest.php` (testes automatizados de fluxo e precificação de variações)

## Critérios de Aceite
- [ ] Migration executada com sucesso adicionando `preco` decimal(10,2) nulo e tornando `cor` e `tamanho` flexíveis em `produto_variacoes`.
- [ ] Cadastro e edição de produtos aceitam qualquer valor de atributo (ex: "128GB", "220V", "41", "P"), cor opcional e preço customizado por SKU.
- [ ] Detalhe do produto na loja exibe rótulo dinâmico apropriado e atualiza o preço na tela ao selecionar variações com preços distintos.
- [ ] Adição ao carrinho e finalização de pedido calculam o preço correto da variação e salvam os atributos.
- [ ] Testes no PHPUnit cobrindo todo o fluxo com 100% de sucesso.
