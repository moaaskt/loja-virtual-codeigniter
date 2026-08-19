---
status: complete
date: 2026-08-19
slug: variacoes-dinamicas-categoria
description: "Adaptação dinâmica da interface de variações/SKUs nos formulários admin (new.php e edit.php) com base na categoria selecionada (Moda/Calçados, Eletrônicos/Eletro e Geral)"
---

# Resumo da Tarefa Rápida: Variações Dinâmicas por Categoria no Admin

## O que foi realizado

1. **Comportamento Dinâmico via JavaScript**:
   - Adicionado listener para o evento `change` no seletor de categoria (`select[name="categoria_id"]`) em [new.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/admin/produtos/new.php) e [edit.php](file:///home/moa-dev/projetos/loja-virtual-codeigniter/app/Views/admin/produtos/edit.php).
   - Identificação inteligente do tipo de categoria através de normalização de texto e expressões regulares:
     - **Moda / Vestuário / Calçados / Tênis**:
       - Título da coluna atualizado para: `"Tamanho / Numeração"`.
       - Placeholder: `"Ex: P, M, G, 38, 41, Único"`.
       - Datalist de sugestões: tamanhos (PP, P, M, G, GG, XG, XGG, Único) e numerações de calçados (34 a 45).
     - **Eletrônicos / Informática / Computadores / Celulares / Eletrodomésticos**:
       - Título da coluna atualizado para: `"Capacidade / Voltagem / Atributo"`.
       - Placeholder: `"Ex: 128GB, 256GB, 110V, 220V, Bivolt"`.
       - Datalist de sugestões: capacidades (128GB, 256GB, 512GB, 1TB, 2TB, 8GB RAM, 16GB RAM, 32GB RAM) e voltagens (110V, 220V, 127V, Bivolt).
     - **Demais Categorias**:
       - Título da coluna: `"Variação / Atributo"`.
       - Placeholder: `"Ex: 128GB, P, 110V, 41"`.
       - Datalist com sugestões genéricas combinadas.
   - Atualização em tempo real dos placeholders das linhas existentes e de quaisquer novas linhas criadas pelo botão *"Adicionar Variação"*.
   - Execução automática ao carregar a página (garantindo que produtos em edição já abram com o rótulo e sugestões corretas de sua categoria atual).

2. **Integridade da Arquitetura e Banco de Dados**:
   - A estrutura das tabelas (`produto_variacoes`) e o mapeamento no controller (`tamanho`, `cor`, `preco`, `estoque`) foram mantidos intactos e universais.

3. **Validação**:
   - Suíte de testes automatizados (`vendor/bin/phpunit`) executada com sucesso (**27 testes e 140 asserções aprovados**, 100% de sucesso).
