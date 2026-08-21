# Requisitos do Sistema — Milestone 3: Experiência do Usuário & Catálogo Avançado

- **Versão:** v3.0
- **Status:** Em Planejamento / Execução
- **Objetivo Geral:** Elevar a experiência do usuário (UX/UI) e a robustez do catálogo a um nível premium de portfólio, resolvendo a flexibilidade de produtos multi-atributos (SKUs complexos), fornecendo seletores interativos modernos na PDP com troca de fotos, painel do cliente ("Minha Conta") com timeline visual de pedidos e lista de desejos (Wishlist).

---

## 📋 Requisitos por Fase

### Phase 10: Arquitetura & Gerador de Variações Multi-Atributos (Admin + Database)
- [ ] **Estrutura de Dados Flexível para Variações:**
  - Suporte a múltiplos atributos por produto (ex: Cor + Armazenamento + RAM para celulares; Cor + Tamanho para roupas; Voltagem + Cor para eletrodomésticos).
  - Tabela / Estrutura de Variações com suporte a SKU, preço próprio, estoque, código de barras/EAN e foto da variação (`imagem_url` / `imagem_id`).
  - Atributos dinâmicos estruturados (ex: JSON ou tabela de pares chave-valor normalizados).
- [ ] **Gerador de Grade de SKUs no Admin:**
  - Interface no cadastro/edição de produtos para definir eixos/atributos e seus valores correspondentes (ex: Cor: `[Azul, Vermelho]`, Armazenamento: `[128GB, 256GB]`).
  - Botão "Gerar Combinações" com criação automática de todas as linhas da matriz.
  - Edição em lote ou individual de preço, estoque e foto por SKU gerado.
- [ ] **Compatibilidade e Integridade de Domínio:**
  - Manutenção de compatibilidade nos services de Carrinho (`CarrinhoService`) e Pedidos (`PedidoService`) para gravação e baixa de estoque do SKU correto.

### Phase 11: Experiência Interativa de Compra na PDP (Storefront UX)
- [ ] **Seletores Modernos e Reativos na PDP:**
  - Seletor de Cor via Swatches visuais circulares (com preview da cor hex, tooltip do nome e borda de seleção ativa).
  - Seletores de outros atributos (Armazenamento, RAM, Tamanho, Voltagem) em formato de Chips/Pills dinâmicos e elegantes.
  - Indicação contextual de diferença de valor nos chips (ex: `+ R$ 300,00`).
- [ ] **Interdependência e Validação Cruzada de Estoque:**
  - Desativação ou estilo riscado ("indisponível") em combinações que estejam sem estoque.
  - Seleção automática da primeira combinação disponível ao carregar a página.
- [ ] **Troca Dinâmica de Fotos por Variação:**
  - Ao selecionar uma cor/variação com foto associada, a foto principal da galeria troca dinamicamente com transição suave.
- [ ] **Atualização Reativa de Preço e Parcelas:**
  - Preço à vista, valor parcelado e economia (se houver desconto) atualizam instantaneamente conforme o SKU ativo.

### Phase 12: Painel "Minha Conta" & Timeline Visual de Pedidos
- [ ] **Área do Cliente Estruturada:**
  - Layout dedicado para `/cliente/conta` com sidebar de navegação rápida (Meus Pedidos, Meus Endereços, Meus Dados, Favoritos).
- [ ] **Timeline Visual de Rastreamento de Pedido:**
  - Visualização de pedido com indicador de progresso passo a passo (Realizado ➔ Aguardando Pagamento ➔ Pago ➔ Em Separação ➔ Enviado com Código de Rastreio ➔ Entregue).
  - Exibição de detalhes dos itens, variação/SKU comprada, endereço de entrega e resumo de pagamentos/códigos Pix.
- [ ] **Gestão de Múltiplos Endereços:**
  - CRUD de endereços do cliente (Principal, Casa, Trabalho) com preenchimento automático por CEP via ViaCEP.
- [ ] **Gestão de Perfil:**
  - Alteração de dados cadastrais e troca segura de senha.

### Phase 13: Lista de Desejos (Wishlist) & Micro-interações de Conversão
- [ ] **Lista de Desejos (Favoritos):**
  - Botão de favoritar (coração interativo com micro-animação de batimento) na vitrine, listagens e PDP.
  - Persistência no banco de dados para clientes logados (e suporte gracioso para visitantes).
  - Tela `/cliente/favoritos` com visualização rápida e botão de "Mover para o Carrinho".
- [ ] **Drawer / Modal de Adicionado ao Carrinho:**
  - Ao clicar em "Comprar / Adicionar", exibir gaveta lateral ou modal elegante confirmando a adição.
  - Sugestão de produtos relacionados / complementares (Cross-sell / Compre Junto).
- [ ] **Toasts e Feedback de Alta Fidelidade:**
  - Notificações não intrusivas (toasts animados) para ações do usuário (adicionado ao carrinho, favoritado, cupom aplicado).
