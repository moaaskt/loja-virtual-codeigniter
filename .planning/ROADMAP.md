# Roadmap de Fases

## Milestone 1: Core E-Commerce & Catálogo Interativo (Concluído ✓)

### Phase 1: Estabilidade Técnica e Segurança (Dívidas Técnicas)
- **Status:** Completed ✓
- **Objetivo:** Resolver as dívidas técnicas críticas e garantir que as rotas estão protegidas.

### Phase 2: Auditoria e Validação do Core (Carrinho, Checkout e Upload)
- **Status:** Completed ✓
- **Objetivo:** Auditar fluxos críticos existentes para garantir funcionamento consistente.

### Phase 3: Novas Funcionalidades (Estoque e Detalhes de Pedidos)
- **Status:** Completed ✓
- **Objetivo:** Implementar variações de produtos (tamanho/cor), baixa de estoque e detalhe de pedidos.

### Phase 4: Busca e Filtros Avançados
- **Status:** Completed ✓
- **Objetivo:** Sistema de busca textual e filtros dinâmicos instantâneos (categorias, marcas, gênero, faixa de preço, debounce 300ms e sync de URL).

---

## Milestone 2: E-Commerce Avançado (Em Andamento 🚀)

### Phase 5: Cálculo de Frete e Cupons de Desconto
- **Status:** Completed ✓
- **Objetivo:** Permitir simulação/cálculo de frete por CEP e criação/aplicação de cupons de desconto promocionais no carrinho e checkout.
- **Tarefas principais:**
  - Implementar cálculo/simulação de frete por CEP na página do produto e no carrinho.
  - Criar estrutura (Migration/Model) e CRUD de Cupons de Desconto no Painel Admin.
  - Implementar validação e aplicação de cupom no Carrinho e Checkout com recálculo automático de totais.

### Phase 6: Gateway de Pagamento (Pix e Cartão de Crédito)
- **Status:** Completed ✓
- **Objetivo:** Integrar fluxo de pagamento transacional com Pix (QR Code/Copia e Cola) e Cartão de Crédito, com webhook de confirmação.
- **Tarefas principais:**
  - Criar serviço de gateway de pagamento para Pix e Cartão de Crédito.
  - Implementar tela de checkout com seleção de forma de pagamento e geração de cobrança.
  - Implementar endpoint de Webhook para recepção de status de pagamento e atualização de pedidos.

### Phase 7: Notificações Transacionais por E-mail
- **Status:** Completed ✓
- **Objetivo:** Configurar serviço de e-mail (SMTP) e disparar notificações automáticas no ciclo de vida dos pedidos.
- **Tarefas principais:**
  - Configurar serviço de envio de e-mails no CodeIgniter 4.
  - Criar templates HTML responsivos para Pedido Realizado, Pagamento Aprovado e Pedido Enviado.
  - Integrar disparos automáticos nos controllers de checkout, webhook e painel admin.

### Phase 7.5: Auditoria do Sistema & Fila de Notificações
- **Status:** Completed ✓
- **Objetivo:** Implementar infraestrutura desacoplada de auditoria de alterações (audit trail) e fila resiliente para registro, monitoramento e reprocessamento de notificações (e-mail/WhatsApp).
- **Tarefas principais:**
  - Criar migrations e models para `audit_logs` e `notification_logs`.
  - Criar `AuditService` global e refatorar `EmailService` para integrar persistência na fila/histórico de notificações.
  - Criar interfaces no Painel Admin para Trilha de Auditoria (`/admin/auditoria`) e Monitor da Fila de Notificações com reprocessamento (`/admin/notificacoes/fila`).
  - Atualizar navegação do Admin (`app/Views/layouts/admin.php`) com links de Auditoria e Fila.
  - Desenvolver suíte de testes unitários e de integração em `tests/app/AuditServiceTest.php` e `NotificationLogTest.php`.

### Phase 8: Avaliações e Reviews de Produtos
- **Status:** Completed ✓
- **Objetivo:** Permitir que clientes avaliem produtos com notas e comentários, exibindo a reputação na vitrine e detalhes do produto.
- **Tarefas principais:**
  - Criar migration, model e controller para avaliações de produtos (1 a 5 estrelas + comentário).
  - Adicionar formulário de avaliação na área do cliente / página do produto com verificação de compra.
  - Exibir estrelas e reviews aprovadas no catálogo e página de detalhes.
  - Criar tela de moderação de avaliações no Painel Admin com trilha de auditoria.

### Phase 9: Relatórios e Métricas Avançadas no Painel Admin
- **Status:** Pending
- **Objetivo:** Fornecer aos administradores visão analítica de vendas, faturamento, produtos mais vendidos e exportação de relatórios.
- **Tarefas principais:**
  - Desenvolver Dashboard analítico com cards de KPIs e gráficos de vendas.
  - Criar relatórios de produtos mais vendidos e clientes com maior volume de pedidos.
  - Implementar exportação de relatórios em CSV.
