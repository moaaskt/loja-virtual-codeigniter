# Plano de Execução — Phase 10: Arquitetura & Gerador de Variações Multi-Atributos (Admin + Database)

## Objetivo
Desenvolver e implementar uma arquitetura de dados e interface administrativa moderna e flexível para **produtos multi-atributos (SKUs complexos)** na Loja Virtual CodeIgniter.

O novo sistema permitirá que produtos de qualquer segmento (como smartphones com Cor + Armazenamento + RAM, roupas com Cor + Tamanho, calçados com Cor + Numeração ou eletrodomésticos com Cor + Voltagem) tenham suas variações geradas automaticamente via **gerador de matriz cartesiana** com 1 clique no Painel Admin, com suporte a **preço individual, estoque, SKU, imagem específica por variação e atributos dinâmicos estruturados em JSON**.

---

## 🏗️ 1. Banco de Dados & Migrations

### Arquivo: `app/Database/Migrations/2026-08-20-170000_UpdateProdutoVariacoesMultiAtributos.php`

- **Novas colunas na tabela `produto_variacoes`**:
  - `sku` (VARCHAR 100, nullable): Código identificador único do SKU (ex: `S24-AZUL-256GB-12GB`).
  - `nome_variacao` (VARCHAR 255, nullable): Rótulo legível da combinação (ex: `Azul / 256GB / 12GB RAM`).
  - `atributos_json` (TEXT / JSON, nullable): Mapa chave-valor estruturado (ex: `{"Cor":"Azul","Armazenamento":"256GB","Memória RAM":"12GB"}`).
  - `imagem_url` (VARCHAR 255, nullable): Foto específica da variação/cor (URL ou path relativo do upload).
  - `codigo_barras` (VARCHAR 50, nullable): Código EAN/GTIN opcional para o SKU.
- **Preservação de Retrocompatibilidade**:
  - Manter as colunas `tamanho`, `cor`, `cor_hex`, `preco`, `estoque`, garantindo funcionamento contínuo de registros e testes legados.

---

## ⚙️ 2. Camada de Model & Services

### `app/Models/ProdutoVariacaoModel.php` (e `ProdutoModel.php`)
- **Campos permitidos (`$allowedFields`)**:
  - Adicionar `sku`, `nome_variacao`, `atributos_json`, `imagem_url`, `codigo_barras`.
- **Métodos Auxiliares**:
  - `getVariacoesFormatadas(int $produtoId): array`: Retorna variações com `atributos_json` decodificado em array, calculando imagem fallback (imagem do produto se a variação não tiver foto própria) e preço efetivo.
  - `getAtributosDisponiveis(int $produtoId): array`: Extrai dinamicamente a lista de atributos e valores distintos disponíveis para os seletores da PDP (Fase 11).

### `app/Services/CarrinhoService.php` & `app/Services/PedidoService.php`
- Enriquecer os itens do carrinho e do pedido com `nome_variacao`, `sku`, `atributos` e a `imagem` específica da variação.
- Manter o cálculo de preço customizado da variação e a baixa atômica de estoque por `variacao_id`.

---

## 🖥️ 3. Interface Administrativa & Gerador de SKUs

### Arquivos: `app/Views/admin/produtos/new.php` e `app/Views/admin/produtos/edit.php`

- **Gerenciador de Atributos do Produto**:
  - Interface para adicionar múltiplos eixos/atributos (ex: *Atributo 1: Cor*, *Atributo 2: Armazenamento*, *Atributo 3: RAM*).
  - Tags/Chips dinâmicos para inserir múltiplos valores por atributo (ex: `[Azul, Vermelho]`, `[128GB, 256GB]`).
  - **Presets Rápidos por Categoria**:
    - *Smartphones/Eletrônicos:* Sugere Cor + Armazenamento + RAM.
    - *Moda/Vestuário:* Sugere Cor + Tamanho.
    - *Calçados:* Sugere Cor + Numeração.
    - *Eletrodomésticos:* Sugere Voltagem + Cor.
- **⚡ Botão "Gerar Combinações (Grade de SKUs)"**:
  - Calcula o produto cartesiano de todos os atributos definidos.
  - Gera automaticamente a tabela de SKUs preenchida com as combinações.
- **Tabela de Variações / SKUs Gerados**:
  - **Identificação:** Rótulo da combinação + campo de SKU editável.
  - **Cor / Swatch:** Color picker + nome da cor (se o atributo Cor estiver presente).
  - **Preço:** Campo individual (placeholder com o preço base do produto).
  - **Estoque:** Quantidade disponível para aquele SKU específico.
  - **Foto da Variação:** Campo de upload ou URL da imagem para a cor/variação (com preview instantâneo).
  - **Barra de Ações em Lote:** Aplicar mesmo preço ou estoque para todas as variações com 1 clique.
  - **Modo Manual:** Possibilidade de adicionar ou remover SKUs individualmente.

---

## 🎮 4. Controlador Administrativo

### Arquivo: `app/Controllers/Admin/ProdutosController.php`

- **Tratamento de Uploads de Imagens de Variações**:
  - Suporte a upload de fotos individuais para cada linha de variação ou preenchimento de URL.
- **Salvamento Estruturado**:
  - Serialização dos atributos em `atributos_json` e concatenação legível em `nome_variacao`.
  - Tratamento transacional no banco de dados (`$db->transStart() / $db->transComplete()`) para garantir integridade.
  - Trilha de auditoria (`AuditService`) registrando criação e edição de variações.

---

## 🧪 5. Testes Automatizados

### Arquivo: `tests/app/VariacoesMultiAtributosTest.php`

- **Casos de Teste Essenciais**:
  1. `testSalvarProdutoComVariacoesMultiAtributos`: Valida persistência de produto com variações contendo `atributos_json`, `sku`, preço diferenciado e foto.
  2. `testGeracaoDeAtributosFormatadosNoModel`: Valida decodificação do JSON de atributos e agrupamento de opções.
  3. `testAdicionarVariacaoMultiAtributoAoCarrinho`: Garante que o carrinho registra o SKU, imagem da variação e preço correto.
  4. `testCheckoutEBaixaDeEstoqueMultiAtributo`: Valida criação do pedido e decremento de estoque específico da variação no banco.
  5. `testRetrocompatibilidadeVariacoesSimples`: Garante que produtos com variações antigas (`tamanho` e `cor` apenas) continuam funcionando 100%.

---

## ✅ Critérios de Aceite da Fase 10

1. Migration executada com sucesso adicionando as novas colunas à tabela `produto_variacoes`.
2. Formulários de criação e edição no Admin permitindo gerar grade automática de SKUs multi-atributos com fotos, preços e estoque individuais.
3. Todas as variações salvas com JSON estruturado e recuperadas corretamente pelo backend.
4. Suíte de testes automatizados passando 100% (todos os 68 testes existentes + novos testes de multi-atributos).
