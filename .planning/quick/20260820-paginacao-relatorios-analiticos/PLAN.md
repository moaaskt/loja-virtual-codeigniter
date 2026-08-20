# Quick Task: Paginação e Otimização dos Relatórios Analíticos

## Objetivo
Implementar paginação nativa (20 itens por página) e controles de navegação nos relatórios administrativos (Vendas, Produtos, Clientes e Cupons), com otimização das consultas SQL no `RelatorioService` e `PedidoModel`.

## Tarefas
1. [x] Implementar `PedidoModel::getVendasRelatorio()` com paginação nativa do CodeIgniter.
2. [x] Adicionar suporte a paginação com `limit`, `offset` e contagens totais no `RelatorioService.php`.
3. [x] Integrar paginação nos métodos `vendas()`, `produtos()`, `clientes()` e `cupons()` do `RelatoriosController.php`.
4. [x] Adicionar componentes de paginação Bootstrap nas views `vendas.php`, `produtos.php`, `clientes.php` e `cupons.php`.
5. [x] Criar testes unitários/integração em `RelatorioTest.php` e validar 100% de sucesso no PHPUnit.
