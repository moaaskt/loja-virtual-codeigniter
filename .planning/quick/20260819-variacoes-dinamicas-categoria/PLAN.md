# Quick Task: Variações Dinâmicas por Categoria no Admin

## Objetivo
Adaptar dinamicamente a interface de variações/SKUs nos formulários de criação e edição de produtos (`app/Views/admin/produtos/new.php` e `edit.php`) com base na categoria selecionada:
1. Escutar o evento `change` no campo `select[name="categoria_id"]` e identificar o tipo da categoria (Moda/Vestuário/Calçados vs Eletrônicos/Informática/Eletro vs Geral).
2. Atualizar o título da coluna da tabela de variações, os placeholders dos inputs e o `datalist` de sugestões correspondente.
3. Manter compatibilidade total com o backend e a estrutura de dados existente (`tamanho`, `cor`, `preco`, `estoque`).

## Arquivos Afetados
- `app/Views/admin/produtos/new.php`
- `app/Views/admin/produtos/edit.php`
- `tests/app/VariacoesGenericasTest.php` (ou testes adicionais de integridade)

## Critérios de Aceite
- [ ] Ao selecionar categorias de Moda/Calçados, o título da coluna muda para "Tamanho / Numeração" e as sugestões priorizam tamanhos (PP a XGG) e números (35 a 44).
- [ ] Ao selecionar categorias de Eletrônicos/Informática/Eletrodomésticos, o título muda para "Capacidade / Voltagem / Atributo" e as sugestões priorizam "128GB", "256GB", "110V", "220V", etc.
- [ ] Demais categorias mantêm o título "Variação / Atributo" com sugestões genéricas.
- [ ] O botão "Adicionar Variação" cria novas linhas respeitando a configuração da categoria selecionada no momento.
- [ ] Ao carregar a página de edição (`edit.php`), a configuração da categoria atual é aplicada imediatamente.
- [ ] Testes no PHPUnit continuam 100% aprovados.
