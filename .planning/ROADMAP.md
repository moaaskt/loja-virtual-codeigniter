# Roadmap de Fases

## Milestones Concluídos

- **[Milestone 1: Core E-Commerce & Catálogo Interativo](milestones/v1.0-ROADMAP.md)** — Concluído ✓ (Fases 1 a 4: Estabilidade, Core, Estoque/Variações, Busca/Filtros)
- **[Milestone 2: E-Commerce Avançado — Pagamentos, Frete, Cupons, Avaliações e Métricas](milestones/v2.0-ROADMAP.md)** — Concluído ✓ (Fases 5 a 9: Frete/Cupons, Gateway Pix/Cartão, Notificações SMTP, Auditoria/Fila, Avaliações/Reviews, Relatórios/BI) — [Auditoria v2.0](v2.0-MILESTONE-AUDIT.md)

---

## Milestone 3: Experiência do Usuário & Catálogo Avançado (100% Concluído ✓)

- **Versão:** v3.0
- **Status:** Concluído com Sucesso ✓
- **Documento de Requisitos:** [REQUIREMENTS.md](REQUIREMENTS.md)

### Fases do Milestone 3

#### Phase 10: Arquitetura & Gerador de Variações Multi-Atributos (Admin + Database)
- **Status:** Completed ✓
- **Objetivo:** Reformular o modelo de variações para suportar múltiplos atributos dinâmicos (Cor, Armazenamento, RAM, Voltagem, Tamanho), gerador automático de grade de SKUs no Painel Admin e foto por SKU.
- **Entregas Realizadas:**
  - Migration `2026-08-20-170000_UpdateProdutoVariacoesMultiAtributos.php` adicionando `sku`, `nome_variacao`, `atributos_json`, `imagem_url` e `codigo_barras`.
  - Model `ProdutoVariacaoModel` com decodificação de JSON e extração dinâmica de atributos.
  - Gerador de grade cartesiana de SKUs com presets rápidos (Smartphones, Moda, Calçados, Eletro) nos formulários `new.php` e `edit.php`.
  - Suporte a fotos por variação/cor, preços individuais e estoque por SKU.
  - Integração no `CarrinhoService` e `PedidoService` para rastreamento de SKUs multi-atributos.
  - Suíte de testes `VariacoesMultiAtributosTest.php` com 100% de aprovação (73 testes, 460 asserções).

#### Phase 11: Experiência Interativa de Compra na PDP (Storefront UX)
- **Status:** Completed ✓
- **Objetivo:** Implementar seletores modernos em swatches e chips na PDP com validação cruzada de estoque, troca de fotos por cor/SKU e recálculo em tempo real de preços e parcelas.
- **Entregas Realizadas:**
  - Seletores visuais dinâmicos de cores (Swatches circulares com hover, ring ativo e label sincronizado).
  - Seletores interativos em Chips/Pills para qualquer outro eixo (Armazenamento, RAM, Voltagem, Tamanho).
  - Engine JavaScript de interdependência e validação cruzada N-dimensional com desativação visual de combinações sem estoque.
  - Troca fluida de imagem principal com transição de opacidade ao selecionar cor/variação com foto.
  - Atualização reativa de preço, parcelamento em 10x sem juros, desconto Pix e badge de SKU ativo.
  - Suíte de testes `PdpInterativaTest.php` com 100% de aprovação (76 testes, 507 asserções).

#### Phase 12: Painel "Minha Conta" & Timeline Visual de Pedidos
- **Status:** Completed ✓
- **Objetivo:** Criar uma central completa do cliente com histórico de pedidos, timeline visual de rastreio de status, múltiplos endereços (ViaCEP) e gestão de perfil.
- **Entregas Realizadas:**
  - Migration `2026-08-21-060000_CreateClienteEnderecosTable.php` e Model `ClienteEnderecoModel.php`.
  - Central do cliente com sidebar modular e navegação ativa (`/minha-conta/pedidos`, `/minha-conta/enderecos`, `/minha-conta/perfil`).
  - **Timeline Visual de 5 Etapas:** Pedido Realizado ➔ Pagamento Confirmado ➔ Em Separação ➔ Enviado com Rastreio ➔ Entregue.
  - Tela detalhada do pedido (`/minha-conta/pedidos/(:num)`) com tabela de itens enriquecida com SKUs e fotos de variações.
  - Gestão de múltiplos endereços de entrega com busca automática integrada via ViaCEP e definição de endereço padrão.
  - Seletor de endereços salvos no Checkout (`checkout.php`).
  - Painel de edição de dados pessoais e alteração de senha segura.
  - Suíte de testes `MinhaContaETimelineTest.php` com 100% de aprovação (80 testes, 555 asserções).

#### Phase 13: Lista de Desejos (Wishlist) & Micro-interações de Conversão
- **Status:** Completed ✓
- **Objetivo:** Desenvolver sistema de Lista de Desejos (Favoritos) com micro-animações, gaveta/modal de carrinho com cross-sell de recomendados e toasts de alta fidelidade.
- **Entregas Realizadas:**
  - Migration `2026-08-21-070000_CreateClienteFavoritosTable.php` e Model `ClienteFavoritoModel.php`.
  - API assíncrona `/api/favoritos/toggle` e `/api/favoritos/ids` com `FavoritoController.php`.
  - Botões interativos de coração com micro-animação elástica (`heartBounce`) na vitrine, nas buscas, nos recomendados e na PDP.
  - Tela dedicada "Minha Lista de Desejos" (`/minha-conta/favoritos`) com fotos, preços, estoque e botão para mover para o carrinho.
  - Contador dinâmico de favoritos no header da loja (`#badge-favoritos-nav`) e atalho no menu dropdown do usuário.
  - Sistema flutuante de Toasts modernos de feedback para micro-interações de alta conversão.
  - Suíte de testes `WishlistEFavoritosTest.php` com 100% de aprovação (85 testes, 652 asserções).
