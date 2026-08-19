---
status: complete
date: 2026-08-19
slug: variacoes-genericas-skus
description: "Refatoração do sistema de variações e SKUs para suporte genérico e flexível (Eletrônicos, Calçados, Roupas), com cor opcional, preço individual por variação e rótulos dinâmicos"
---

# Resumo da Tarefa Rápida: Variações e SKUs Genéricos e Flexíveis

## O que foi realizado

1. **Inspeção e Refatoração da Base de Dados**:
   - Criada migration `2026-08-19-224500_UpdateProdutoVariacoesGenericas.php` que adicionou a coluna `preco` (`decimal(10,2) NULL`) e tornou as colunas `cor` e `tamanho` flexíveis (`VARCHAR(100) NULL`).
   - Atualizado o schema em `banco_loja.sql`.
   - Migration executada e validada com sucesso no MySQL do Docker.

2. **Refatoração do Painel Admin (Cadastro e Edição de Produtos)**:
   - **Campos Flexíveis e Livres**: Substituído o select engessado de tamanhos fixos (P/M/G) por um campo aberto **Variação / Atributo** com `datalist` de sugestões inteligentes (ex: "128GB", "256GB", "110V", "220V", "41", "P", "M", "Único").
   - **Cor Opcional**: O campo de Cor agora é opcional (permitindo produtos sem cor como eletrônicos/eletrodomésticos padrão, ou cores livres como "Preto", "Titânio", "Azul").
   - **Preço Individual por Variação**: Adicionado campo opcional de preço customizado por SKU (se vazio, herda o preço base do produto).
   - **Estoque por Variação**: Controle de estoque individual e soma automática do estoque total do produto no [ProdutosController.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Controllers/Admin/ProdutosController.php).
   - Atualizadas as views [new.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/admin/produtos/new.php) e [edit.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/admin/produtos/edit.php).

3. **Refatoração da Vitrine e Detalhe do Produto (Frontend)**:
   - **Rótulo Dinâmico Inteligente**: Na página de produto ([produto_detalhe.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/shop/produto_detalhe.php)), o rótulo do seletor é detectado automaticamente de acordo com os atributos (ex: "Capacidade / Modelo" para eletrônicos, "Voltagem" para eletrodomésticos, "Tamanho" para roupas/calçados, ou "Variação / Opção").
   - **Atualização de Preço em Tempo Real**: Ao selecionar uma variação com preço diferenciado (ex: iPhone 256GB mais caro que 128GB), o preço e estoque exibidos na tela são atualizados instantaneamente via JavaScript.
   - **Seleção Flexível**: Suporta produtos com apenas atributos, apenas cores, ambos ou nenhum.

4. **Carrinho, Checkout e Pedidos**:
   - [CarrinhoService.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Services/CarrinhoService.php) atualizado para precificar o item conforme o preço customizado da variação e emitir mensagens genéricas amigáveis.
   - [PedidoService.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Services/PedidoService.php) atualizado para consolidar subtotal e itens de pedido com o preço unitário correto da variação e dar baixa no estoque do SKU específico.
   - Atualizada a exibição de variações nos templates [carrinho.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/shop/carrinho.php), [checkout.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/shop/checkout.php), [pedido_pagamento.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/shop/pedido_pagamento.php), [meus_pedidos.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/cliente/meus_pedidos.php) e [detalhe.php (admin)](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/admin/pedidos/detalhe.php).

5. **Testes Automatizados**:
   - Criada a suíte de testes [VariacoesGenericasTest.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/tests/app/VariacoesGenericasTest.php) cobrindo cadastro com variações flexíveis, precificação customizada no carrinho, checkout e baixa de estoque.
   - 100% dos testes no PHPUnit aprovados (27 testes, 140 asserções).
