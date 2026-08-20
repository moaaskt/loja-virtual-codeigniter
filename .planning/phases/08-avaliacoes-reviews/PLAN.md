# Plano de Execução — Phase 8: Avaliações e Reviews de Produtos

## Objetivo
Implementar um sistema completo, robusto e moderno de **Avaliações e Reviews de Produtos** no e-commerce, permitindo que clientes autenticados avaliem produtos com notas de 1 a 5 estrelas e comentários, com detecção automática de compra verificada, exibição dinâmica de médias e reputação na vitrine e página de detalhes do produto, e painel administrativo completo de moderação e auditoria.

---

## Detalhamento das Tarefas de Implementação

### 1. Banco de Dados & Migrations

#### A. Migration `CreateAvaliacoesTable`
- **Arquivo**: `app/Database/Migrations/2026-08-20-001003_CreateAvaliacoesTable.php`
- **Estrutura da Tabela `avaliacoes`**:
  - `id`: `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
  - `produto_id`: `INT UNSIGNED NOT NULL` (Foreign Key para `produtos.id` `ON DELETE CASCADE`)
  - `usuario_id`: `INT UNSIGNED NOT NULL` (Foreign Key para `usuarios.id` `ON DELETE CASCADE`)
  - `pedido_id`: `INT UNSIGNED NULL` (Foreign Key opcional para `pedidos.id` `ON DELETE SET NULL`)
  - `nota`: `TINYINT UNSIGNED NOT NULL` (1 a 5)
  - `titulo`: `VARCHAR(150) NULL`
  - `comentario`: `TEXT NOT NULL`
  - `status`: `ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente'`
  - `compra_verificada`: `TINYINT(1) DEFAULT 0`
  - `created_at`: `DATETIME NULL`
  - `updated_at`: `DATETIME NULL`
- **Índices**:
  - `produto_id`, `usuario_id`, `status`, `created_at`, `compra_verificada`.

#### B. Model `App\Models\AvaliacaoModel`
- **Arquivo**: `app/Models/AvaliacaoModel.php`
- **Atributos**:
  - `$table = 'avaliacoes'`, `$primaryKey = 'id'`, `$useTimestamps = true`
  - `$allowedFields = ['produto_id', 'usuario_id', 'pedido_id', 'nota', 'titulo', 'comentario', 'status', 'compra_verificada', 'created_at', 'updated_at']`
- **Regras de Validação**:
  - `produto_id`: `required|is_natural_no_zero`
  - `usuario_id`: `required|is_natural_no_zero`
  - `nota`: `required|in_list[1,2,3,4,5]`
  - `comentario`: `required|min_length[5]|max_length[2000]`
  - `titulo`: `permit_empty|max_length[150]`
  - `status`: `permit_empty|in_list[pendente,aprovada,rejeitada]`
- **Métodos Auxiliares**:
  - `getAvaliacoesPorProduto(int $produtoId, int $limit = 10, bool $apenasAprovadas = true): array`: Retorna avaliações com dados do usuário (nome) e formato ordenado por data decrescente.
  - `getEstatisticasProduto(int $produtoId): array`: Calcula e retorna média ponderada (`media`), total de avaliações (`total`), contagem por estrela (`distribuicao` [5=>x, 4=>y, ...]) e porcentagens (`percentuais`).
  - `usuarioPodeAvaliar(int $usuarioId, int $produtoId): array`: Verifica se o usuário já avaliou o produto, se possui compra verificada (pedido com status `pago`, `enviado` ou `entregue`) e retorna o `pedido_id` correspondente.
  - `getAvaliacoesComFiltros(array $filtros = [], int $perPage = 15): array`: Listagem paginada para o painel admin com joins em produtos e usuários, filtros por status, produto, cliente e nota.
  - `getContadoresStatus(): array`: Retorna contadores para dashboard de moderação (total, pendentes, aprovadas, rejeitadas, média geral).

---

### 2. Helpers & Utilitários de Visualização

#### A. Helper de Estrelas (`app/Helpers/avaliacao_helper.php` ou extensão de `status_helper.php`)
- Função `renderEstrelas(float $nota, string $size = 'sm', bool $showNumber = false): string`:
  - Gera HTML responsivo com ícones do Bootstrap (`bi-star-fill`, `bi-star-half`, `bi-star`) e cor dourada/âmbar (`#ffc107`).
- Função `getBadgeStatusAvaliacao(string $status): string`:
  - Retorna badge formatado para o painel administrativo (`pendente` -> amarelo, `aprovada` -> verde, `rejeitada` -> vermelho).

---

### 3. Controller do Cliente & Rotas Públicas

#### A. Controller `App\Controllers\AvaliacaoController`
- **Arquivo**: `app/Controllers/AvaliacaoController.php`
- **Métodos**:
  - `enviar()`:
    - Rota `POST /avaliacao/enviar` (Protegida por filtro `auth`).
    - Validação de entrada (`produto_id`, `nota`, `titulo`, `comentario`).
    - Verificação de duplicidade: se o usuário já avaliou, atualiza a avaliação existente ou impede reenvio redundante.
    - Verificação de compra: detecta se o usuário comprou o produto em pedido com status `pago`/`enviado`/`entregue` e marca `compra_verificada = 1` com o `pedido_id`.
    - Persistência com status `pendente` (para moderação).
    - Registro de auditoria via `AuditService::log('create', 'avaliacoes', $id, ...)`.
    - Resposta JSON para requisições AJAX ou Redirect com Flashdata para submissões normais.
  - `listarApi(int $produtoId)`:
    - Rota `GET /api/produtos/(:num)/avaliacoes` (Pública).
    - Retorna JSON com estatísticas (`media`, `total`, `distribuicao`) e lista de avaliações aprovadas.

#### B. Atualização do `HomeController`
- Em `HomeController::produto($id)`:
  - Carregar estatísticas de avaliações do produto e lista de avaliações aprovadas via `AvaliacaoModel`.
  - Se o usuário estiver autenticado, carregar o status de `usuarioPodeAvaliar()`.
  - Passar `$avaliacoes`, `$estatisticasAvaliacao` e `$podeAvaliar` para a view `shop/produto_detalhe`.

---

### 4. Interface da Loja (Frontend / Vitrine)

#### A. Página de Detalhes do Produto (`app/Views/shop/produto_detalhe.php`)
- **Badge de Reputação no Topo**:
  - Ao lado do título/preço, exibir nota média em estrelas, score (ex: 4.8) e link âncora clicável para rolagem suave até a seção de avaliações (ex: `(14 avaliações)`).
- **Seção Completa de Avaliações (`#secao-avaliacoes`)**:
  - **Card de Resumo Geral**:
    - Nota média grande em destaque (ex: 4.8 / 5).
    - Estrelas preenchidas com precisão visual.
    - Barras de progresso elegantes para cada nota (5★ a 1★) com percentual e contagem.
    - Total de recomendações e selo de compras verificadas.
  - **Formulário Interativo de Avaliação**:
    - Seletor dinâmico de estrelas com hover/click interativo e rótulos contextuais ("Péssimo", "Ruim", "Bom", "Muito Bom", "Excelente!").
    - Campo para título da avaliação e textarea para comentário detalhado.
    - Estado para usuário deslogado: Card convidativo com botão para login.
    - Estado para usuário que já avaliou: Mensagem amigável informando que a avaliação já foi enviada.
  - **Lista de Reviews Aprovadas**:
    - Card individual para cada review com:
      - Estrelas da nota.
      - Título e comentário.
      - Nome do autor (com avatar de iniciais estilizado).
      - Data da avaliação formatada (ex: `20 de Agosto de 2026`).
      - Badge visual "Compra Verificada ✓".
  - **Estado Vazio (Empty State)**:
    - Mensagem amigável convidando o primeiro comprador a avaliar o produto.

#### B. Cards de Produtos na Vitrine e Busca (`app/Views/shop/index.php`)
- Exibir estrelas e contagem de avaliações nos cards dos produtos na vitrine principal e no carrossel de produtos relacionados.

#### C. Histórico de Pedidos do Cliente (`app/Views/cliente/meus_pedidos.php`)
- Nos itens de pedidos com status `pago` ou `enviado`, adicionar botão "Avaliar Produto" com link direto para o formulário de avaliação do produto.

---

### 5. Painel Administrativo de Moderação (`/admin/avaliacoes`)

#### A. Controller `App\Controllers\Admin\AvaliacoesController`
- **Arquivo**: `app/Controllers/Admin\AvaliacoesController.php`
- **Métodos**:
  - `index()`: Listagem paginada com filtros por status (`todos`, `pendente`, `aprovada`, `rejeitada`), busca textual, nota e contadores gerais.
  - `aprovar(int $id)`: Altera status para `aprovada` e registra no `AuditService`.
  - `rejeitar(int $id)`: Altera status para `rejeitada` e registra no `AuditService`.
  - `delete(int $id)`: Exclui o registro e registra no `AuditService`.
  - `bulkAction()`: Ação em massa (aprovar múltiplos, rejeitar múltiplos).

#### B. View `app/Views/admin/avaliacoes/index.php`
- Interface administrativa premium com:
  - Cards de métricas rápidas: Total de Reviews, Pendentes de Moderação (com badge de destaque), Aprovadas, Rejeitadas e Média Geral da Loja.
  - Barra de filtros com pesquisa rápida (cliente, produto, comentário), filtro por status e filtro por nota (1 a 5 estrelas).
  - Tabela responsiva com:
    - Miniatura e nome do produto com link para a loja.
    - Nome e e-mail do cliente + badge "Compra Verificada".
    - Nota em estrelas douradas e título/comentário.
    - Data de envio.
    - Status com badge colorido.
    - Ações rápidas (Aprovar com 1 clique, Rejeitar, Excluir com confirmação modal).

#### C. Menu Lateral do Admin (`app/Views/layouts/admin.php`)
- Adicionar item "Avaliações" na seção "Gestão" do menu lateral com ícone representativo (`bi-star-half` ou `bi-chat-square-quote-fill`) e badge numérico caso existam avaliações pendentes.

---

### 6. Rotas (`app/Config/Routes.php`)
- Rotas Públicas & Cliente:
  - `POST avaliacao/enviar` -> `AvaliacaoController::enviar` (filtro `auth`)
  - `GET api/produtos/(:num)/avaliacoes` -> `AvaliacaoController::listarApi/$1`
- Rotas Admin:
  - `GET admin/avaliacoes` -> `Admin\AvaliacoesController::index`
  - `post admin/avaliacoes/aprovar/(:num)` -> `Admin\AvaliacoesController::aprovar/$1`
  - `post admin/avaliacoes/rejeitar/(:num)` -> `Admin\AvaliacoesController::rejeitar/$1`
  - `post admin/avaliacoes/delete/(:num)` -> `Admin\AvaliacoesController::delete/$1`
  - `post admin/avaliacoes/bulk` -> `Admin\AvaliacoesController::bulkAction`

---

### 7. Testes Automatizados (PHPUnit)

#### A. Arquivo `tests/app/AvaliacaoTest.php`
- **Casos de Teste**:
  - `testCriarAvaliacaoComSucesso()`: Valida inserção, campos obrigatórios e cálculo de timestamps.
  - `testValidacaoRegrasNotaEComentario()`: Valida rejeição de notas inválidas (< 1 ou > 5) e comentários vazios/curtos.
  - `testCalculoEstatisticasProduto()`: Valida cálculo exato de média aritmética, total e distribuição percentual de notas.
  - `testDeteccaoCompraVerificada()`: Valida que clientes com pedidos pagos são marcados com `compra_verificada = 1`.
  - `testPrevenirAvaliacaoDuplicada()`: Valida comportamento ao enviar segunda avaliação para o mesmo produto.
  - `testModeracaoAprovarRejeitarAdmin()`: Valida transições de status (`pendente` -> `aprovada` / `rejeitada`) e integração com `AuditService`.
  - `testExclusaoAvaliacaoAdmin()`: Valida exclusão com soft/hard delete e remoção da média do produto.

---

## Critérios de Aceite da Fase 8
- [ ] Migration `CreateAvaliacoesTable` executada e testada no MySQL.
- [ ] `AvaliacaoModel` criado com regras de validação, cálculo estatístico de médias e verificação de compra.
- [ ] Helpers visuais de estrelas e badges criados.
- [ ] `AvaliacaoController` implementado com endpoint seguro de envio e validação.
- [ ] Página de produto (`produto_detalhe.php`) exibindo estrelas no cabeçalho, resumo analítico, formulário dinâmico de avaliação e lista de comentários aprovados com selo de compra verificada.
- [ ] Exibição de nota/estrelas nos cards da vitrine (`index.php`) e botão de avaliar em "Meus Pedidos" (`meus_pedidos.php`).
- [ ] Painel Admin de Moderação (`/admin/avaliacoes`) funcional com filtros, estatísticas e ações de aprovação/rejeição/exclusão com registro na trilha de auditoria.
- [ ] Menu do painel administrativo atualizado com o link para Avaliações.
- [ ] Suíte de testes `AvaliacaoTest.php` criada com 100% de aprovação no PHPUnit.
