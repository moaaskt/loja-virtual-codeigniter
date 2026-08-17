# Roadmap de Fases

## Phase 1: Estabilidade Técnica e Segurança (Dívidas Técnicas)
- **Status:** Pending
- **Objetivo:** Resolver as dívidas técnicas críticas e garantir que as rotas estão protegidas.
- **Tarefas principais:**
  - Corrigir incompatibilidade do PHP 7.4 vs 8.1 no Dockerfile.
  - Ajustar o DocumentRoot do Apache para servir `/public`.
  - Corrigir erro de digitação do volume do banco no docker-compose.
  - Validar os filtros de autenticação e proteção de rotas admin.
  - Remover `$indexPage = 'index.php'` no CI4 para URLs limpas.

## Phase 2: Auditoria e Validação do Core (Carrinho, Checkout e Upload)
- **Status:** Pending
- **Objetivo:** Auditar fluxos críticos existentes para garantir que estão 100% funcionais antes de adicionar complexidade.
- **Tarefas principais:**
  - Testar todo o fluxo do Carrinho de Compras.
  - Testar e corrigir problemas no Checkout e na persistência de Pedidos.
  - Auditar e ajustar o sistema de upload de imagens (exibição correta no Painel Admin).

## Phase 3: Novas Funcionalidades (Estoque e Detalhes de Pedidos)
- **Status:** Pending
- **Objetivo:** Implementar as regras de negócios pendentes que agregam valor ao produto.
- **Tarefas principais:**
  - Implementar estrutura de variação de produtos (tamanho/cor).
  - Implementar baixa automática de estoque na finalização de compra.
  - Melhorar visualização de detalhes de pedidos no Painel Admin e do Cliente.

## Phase 4: Busca e Filtros Avançados
- **Status:** Pending
- **Objetivo:** Facilitar a vida do cliente na vitrine da loja.
- **Tarefas principais:**
  - Implementar sistema de busca de produtos.
  - Implementar filtragem dinâmica por categorias na vitrine da loja.
