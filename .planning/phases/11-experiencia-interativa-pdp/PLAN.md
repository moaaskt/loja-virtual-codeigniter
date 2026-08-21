# Plano de Execução — Phase 11: Experiência Interativa de Compra na PDP (Storefront UX)

## Objetivo
Desenvolver uma **experiência de compra moderna, interativa e fluida** na Página de Detalhes do Produto (PDP) da Loja Virtual CodeIgniter, integrando o novo sistema de multi-atributos (SKUs complexos) da Fase 10 com seletores inteligentes em swatches e chips, validação cruzada de estoque em tempo real, troca instantânea de fotos por variação/cor e recálculo reativo de preços e parcelas.

---

## 🎨 1. Front-end & Componentes Interativos da PDP

### Arquivo: `app/Views/shop/produto_detalhe.php`

- **Renderização Dinâmica de Seletores Multi-Atributos:**
  - Extração inteligente de todos os eixos de atributos disponíveis no produto (`Cor`, `Armazenamento`, `Memória RAM`, `Tamanho`, `Voltagem`, etc.).
  - **Eixo Cor (Swatches Visuais):**
    - Swatches circulares elegantes com preview da cor hexadecimal ou nome, anel de foco/seleção ativa, tooltip e label reativo com o nome da cor selecionada.
  - **Demais Eixos (Chips / Pills Interativos):**
    - Chips modernos com feedback visual de hover/foco.
    - Badges contextuais de variação de preço (ex: `+ R$ 300,00`).
    - Estado de esgotado (`disabled / line-through`) quando uma combinação específica não tiver estoque.
  - **Badges de Confiança e SKU:**
    - Exibição dinâmica do SKU ativo (ex: `SKU: IPHONE15-AZUL-256GB`).
    - Indicador de estoque em tempo real (`Em estoque: X unidades` ou badge de urgência `Restam apenas 2 unidades!`).

- **Troca Dinâmica de Fotos por Variação / Cor:**
  - Transição suave na imagem principal da PDP (`#main-product-img`) para a foto da cor selecionada (`imagem_url` da variação).
  - Sincronização com miniaturas da galeria.

- **Recálculo Reativo de Preços e Condições:**
  - Atualização instantânea do valor à vista (`#pdp-price`).
  - Recálculo das opções de parcelamento (ex: `10x de R$ 449,90 sem juros`) e valor com desconto no Pix.

---

## ⚡ 2. Lógica JavaScript Reativa (Storefront Engine)

### Arquivo: `app/Views/shop/produto_detalhe.php` (script section)

- **Algoritmo de Interdependência N-Dimensional:**
  - Mapeamento de todas as variações em matriz JSON serializada.
  - Ao selecionar qualquer atributo (ex: `Cor: Azul`), reavalia imediatamente a disponibilidade de todos os outros seletores (Armazenamento, RAM).
  - Auto-seleção inteligente da primeira combinação válida e em estoque ao carregar a página.
- **Validação de Formulário:**
  - Garantia de que o campo `variacao_id` esteja preenchido com o SKU correto antes de disparar o `form-add-cart`.
  - Ajuste dinâmico do `max` do input de quantidade conforme o estoque do SKU selecionado.

---

## ⚙️ 3. Integração com Backend & Controller

### Arquivo: `app/Controllers/HomeController.php`
- Enviar as variações enriquecidas via `ProdutoVariacaoModel::getVariacoesFormatadas($produtoId)` para a view `produto_detalhe`.
- Garantir compatibilidade total com produtos sem variações ou com variações simples legadas.

---

## 🧪 4. Testes Automatizados

### Arquivo: `tests/app/PdpInterativaTest.php`
- **Casos de Teste**:
  1. `testPdpCarregaProdutoComVariacoesMultiAtributos`: Valida que a rota `/produto/(:num)` retorna status 200 e inclui dados de SKUs, atributos JSON e imagens no payload da view.
  2. `testPdpRenderizaSwatchesECores`: Valida a presença de swatches de cores e seletores de atributos na resposta HTML.
  3. `testAdicionarAoCarrinhoComSkuMultiAtributoDaPdp`: Valida a submissão de compra com o `variacao_id` selecionado na PDP.
  4. `testRetrocompatibilidadePdpSemVariacao`: Garante que produtos simples continuam permitindo compra direta sem erros.

---

## ✅ Critérios de Aceite da Fase 11

1. Página de produto renderizando seletores dinâmicos em swatches e chips para qualquer combinação de atributos.
2. Troca de foto principal funcionando ao selecionar cores que possuam fotos cadastradas.
3. Desativação visual de combinações sem estoque e atualização de preços/parcelas em tempo real.
4. Todos os testes automatizados (novos + 73 existentes) passando com 100% de sucesso.
