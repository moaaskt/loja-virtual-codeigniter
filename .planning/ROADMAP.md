# Roadmap de Fases

## Milestones Concluídos

- **[Milestone 1: Core E-Commerce & Catálogo Interativo](milestones/v1.0-ROADMAP.md)** — Concluído ✓ (Fases 1 a 4: Estabilidade, Core, Estoque/Variações, Busca/Filtros)
- **[Milestone 2: E-Commerce Avançado — Pagamentos, Frete, Cupons, Avaliações e Métricas](milestones/v2.0-ROADMAP.md)** — Concluído ✓ (Fases 5 a 9: Frete/Cupons, Gateway Pix/Cartão, Notificações SMTP, Auditoria/Fila, Avaliações/Reviews, Relatórios/BI) — [Auditoria v2.0](v2.0-MILESTONE-AUDIT.md)

---

## Milestone 3: Experiência do Usuário & Catálogo Avançado (Ativo)

- **Versão:** v3.0
- **Status:** Em Andamento
- **Documento de Requisitos:** [REQUIREMENTS.md](REQUIREMENTS.md)

### Fases do Milestone 3

#### Phase 10: Arquitetura & Gerador de Variações Multi-Atributos (Admin + Database)
- **Status:** Ready to Plan
- **Objetivo:** Reformular o modelo de variações para suportar múltiplos atributos dinâmicos (Cor, Armazenamento, RAM, Voltagem, Tamanho), gerador automático de grade de SKUs no Painel Admin e foto por SKU.
- **Entregas Principais:**
  - Migrations e Models para atributos/SKUs multi-dimensionais.
  - Gerador de combinações em lote no formulário de produtos do Admin.
  - Vínculo de fotos específicas por variação.
  - Atualização dos Services de Carrinho e Pedido para consumo de SKUs multi-atributos.

#### Phase 11: Experiência Interativa de Compra na PDP (Storefront UX)
- **Status:** Pending (Depende da Phase 10)
- **Objetivo:** Implementar seletores modernos em swatches e chips na PDP com validação cruzada de estoque, troca de fotos por cor/SKU e recálculo em tempo real de preços e parcelas.
- **Entregas Principais:**
  - Seletores interativos (Swatches visuais para cores e Chips/Pills para outros atributos).
  - Lógica JavaScript de interdependência de opções e bloqueio de combinações sem estoque.
  - Troca dinâmica e fluida da galeria/foto principal.
  - Atualização reativa de preços, parcelamento e botão de compra.

#### Phase 12: Painel "Minha Conta" & Timeline Visual de Pedidos
- **Status:** Pending
- **Objetivo:** Criar uma central completa do cliente com histórico de pedidos, timeline visual de rastreio de status, múltiplos endereços (ViaCEP) e gestão de perfil.
- **Entregas Principais:**
  - Área do cliente `/cliente/conta` com navegação modular.
  - Timeline visual de status do pedido (Aguardando Pagamento ➔ Pago ➔ Em Separação ➔ Enviado ➔ Entregue).
  - CRUD de múltiplos endereços de entrega com busca de CEP.
  - Edição de perfil e troca de senha.

#### Phase 13: Lista de Desejos (Wishlist) & Micro-interações de Conversão
- **Status:** Pending
- **Objetivo:** Desenvolver sistema de Lista de Desejos (Favoritos) com micro-animações, gaveta/modal de carrinho com cross-sell de recomendados e toasts de alta fidelidade.
- **Entregas Principais:**
  - Sistema de favoritos (banco + interface com coração animado).
  - Tela `/cliente/favoritos` e ação de mover para o carrinho.
  - Drawer / Modal moderno de "Adicionado ao Carrinho" com produtos relacionados.
  - Toasts animados de feedback e micro-interações.
