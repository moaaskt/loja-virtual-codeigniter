# Requisitos do Sistema — Milestone 2

## Requisitos Concluídos (Milestone 1)
- [x] Login seguro, autenticação e proteção de rotas administrativas (Filtros CI4).
- [x] CRUD completo de Produtos, Categorias e Variações (tamanho/cor).
- [x] Upload de imagens (principal e galeria) e gestão de estoque.
- [x] Catálogo de produtos (Vitrine) com paginação, busca e filtros instantâneos (categorias, marcas, gêneros, faixa de preço, debounce e sync de URL).
- [x] Carrinho de compras em sessão com adição/atualização/remoção.
- [x] Checkout inicial e persistência de pedidos no banco de dados com baixa automática de estoque.
- [x] Visualização de pedidos no painel do cliente e painel administrativo.

---

## Requisitos Escopados para o Milestone 2

### 1. Frete e Cupons de Desconto (Phase 5)
- [x] **Cálculo de Frete**: Simulação de frete por CEP na página de detalhes do produto e cálculo dinâmico no carrinho/checkout (com opção de frete grátis e prazos estimados).
- [x] **Gestão de Cupons (Admin)**: CRUD de cupons de desconto (código, tipo fixo ou porcentagem, valor de desconto, valor mínimo de pedido, limite de usos e data de validade).
- [x] **Aplicação de Cupons (Cliente)**: Campo para aplicar cupom no carrinho e checkout com validação em tempo real e recálculo do valor total.

### 2. Gateway de Pagamento (Phase 6)
- [x] **Pagamento via Pix**: Geração de QR Code e código Copia e Cola para pagamento instantâneo.
- [x] **Pagamento via Cartão de Crédito**: Formulário com validação de dados de cartão e parcelamento.
- [x] **Webhook / Callback de Pagamento**: Atualização automática do status do pedido (Pendente -> Pago -> Cancelado).

### 3. Notificações Transacionais por E-mail & Auditoria (Phase 7 & 7.5)
- [x] **Configuração de E-mail (SMTP)**: Serviço de envio de e-mails integrado ao CodeIgniter 4 com fila e histórico.
- [x] **Templates Responsivos de E-mail**:
  - E-mail de Boas-vindas / Criação de Conta.
  - E-mail de Pedido Realizado com resumo dos itens e instruções de pagamento.
  - E-mail de Confirmação de Pagamento.
  - E-mail de Pedido Enviado / Atualização de Status com rastreio.
- [x] **Disparo de Eventos & Trilha de Auditoria**: Gatilhos automáticos no ciclo de vida dos pedidos e auditoria completa.

### 4. Avaliações e Reviews de Produtos (Phase 8)
- [x] **Estrutura de Avaliações**: Tabela e Model para avaliações (produto_id, usuario_id, nota de 1 a 5 estrelas, título, comentário, status de aprovação e compra verificada).
- [x] **Formulário de Avaliação**: Permitir que clientes autenticados avaliem produtos com notas e comentários dinâmicos.
- [x] **Exibição na Vitrine e Detalhes**: Média de notas com estrelas, score, distribuição e listagem de comentários aprovados com selo de compra verificada.
- [x] **Moderação no Painel Admin**: Aprovação, rejeição e exclusão de comentários com registro na trilha de auditoria.

### 5. Relatórios e Métricas no Painel Admin (Phase 9)
- [ ] **Dashboard Analítico**: Gráficos e cards com faturamento total, faturamento no mês/semana, total de pedidos e ticket médio.
- [ ] **Rankings de Performance**: Produtos mais vendidos, categorias mais lucrativas e clientes com maior volume de compras.
- [ ] **Exportação de Relatórios**: Exportação de listagem de vendas e faturamento em formato CSV/Excel.

