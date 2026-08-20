# Loja Virtual CodeIgniter

## Propósito do Projeto
Projeto full-stack de uma loja virtual (e-commerce) completa, desenvolvida como parte de um estudo aprofundado do framework PHP CodeIgniter 4.

## Público-Alvo
- **Clientes da Loja Virtual**: Usuários que navegam pelo catálogo, aplicam filtros instantâneos, adicionam itens ao carrinho, calculam frete, aplicam cupons, avaliam produtos e finalizam compras via Pix ou Cartão.
- **Administradores**: Gestores da loja que acessam o painel administrativo para gerenciar produtos, categorias, variações, pedidos, cupons de desconto, moderação de avaliações e visualização de relatórios analíticos de vendas.

## Arquitetura de Alto Nível
O projeto utiliza o padrão MVC nativo do CodeIgniter 4. O banco de dados é o MySQL e o ambiente de desenvolvimento é rodado sobre Docker e Docker Compose. O front-end usa HTML5, CSS3 moderno, JavaScript assíncrono (Fetch/AJAX com debounce) e Bootstrap 5.

## Histórico de Milestones

### Milestone 1: Core E-Commerce & Catálogo Interativo (Concluído ✓)
- Configuração de ambiente e correção de dívidas técnicas (PHP/Docker).
- Estrutura completa de autenticação e proteção de rotas administrativas.
- CRUD de produtos, categorias, upload de imagens e gerenciamento de variações/estoque.
- Fluxo de vitrine interativa com busca textual e filtros instantâneos (categorias, marcas, gênero, preço).
- Carrinho de compras e fluxo inicial de checkout.

### Milestone 2: E-Commerce Avançado — Pagamentos, Frete, Cupons, Avaliações e Métricas (Concluído ✓)
- **Cálculo de Frete e Cupons de Desconto**: Simulação de frete na vitrine/carrinho e sistema de cupons com regras de desconto.
- **Gateway de Pagamento**: Processamento de pagamentos via Pix (QR Code/Copia e Cola) e Cartão de Crédito com sincronização de status de pedidos.
- **Notificações Transacionais**: Disparo de e-mails de confirmação de pedido, pagamento e atualizações de entrega via SMTP com fila resiliente.
- **Trilha de Auditoria**: Registro completo de alterações e histórico de operações administrativas.
- **Avaliações e Reviews de Produtos**: Sistema de classificação com estrelas, comentários de clientes, restrição de comprador verificado e moderação no admin.
- **Dashboard Analítico e Relatórios**: Métricas de faturamento, ticket médio, produtos mais vendidos, gráficos no Chart.js, paginação nativa e exportação CSV universal.

