# Loja Virtual CodeIgniter

## Propósito do Projeto
Projeto full-stack de uma loja virtual (e-commerce) completa, desenvolvida como parte de um estudo aprofundado do framework PHP CodeIgniter 4 e com foco em alto padrão de engenharia, arquitetura limpa e experiência do usuário (UX/UI para portfólio).

## Público-Alvo
- **Clientes da Loja Virtual**: Usuários que navegam pelo catálogo, aplicam filtros instantâneos, selecionam variações complexas de produtos com fotos dinâmicas, salvam itens na wishlist, calculam frete, aplicam cupons, avaliam produtos, acompanham o status dos pedidos em timeline visual e finalizam compras via Pix ou Cartão.
- **Administradores**: Gestores da loja que acessam o painel administrativo para gerenciar produtos e matrizes de SKUs multi-atributos, categorias, pedidos, cupons de desconto, moderação de avaliações e visualização de relatórios analíticos de vendas.

## Arquitetura de Alto Nível
O projeto utiliza o padrão MVC nativo do CodeIgniter 4. O banco de dados é o MySQL e o ambiente de desenvolvimento é rodado sobre Docker e Docker Compose. O front-end usa HTML5, CSS3 moderno, JavaScript assíncrono (Fetch/AJAX com debounce) e Bootstrap 5 com componentes e micro-animações personalizadas.

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

### Milestone 3: Experiência do Usuário & Catálogo Avançado (Concluído ✓)
- **Variações Multi-Atributos & Gerador de SKUs**: Suporte a múltiplos eixos (Cor, Armazenamento, RAM, Voltagem, Tamanho), gerador de matriz no admin com 4 presets e imagem vinculada por variação.
- **Storefront Interativo na PDP**: Seletores inteligentes em swatches e pills/chips, validação cruzada de estoque em tempo real, troca suave de fotos e recálculo reativo de preços/parcelas.
- **Painel "Minha Conta" & Rastreio**: Histórico completo de pedidos com timeline visual de status em 5 etapas, gestão de múltiplos endereços (ViaCEP), integração no checkout e perfil do cliente.
- **Lista de Desejos & Micro-interações de Compra**: Wishlist completa com micro-animações elásticas, tela `/minha-conta/favoritos`, contador dinâmico na navbar e toasts modernos de conversão.
