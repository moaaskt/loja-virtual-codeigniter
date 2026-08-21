# Plano de Execução — Phase 13: Lista de Desejos (Wishlist) & Micro-interações de Conversão

## Objetivo
Desenvolver o sistema completo de **Lista de Desejos (Favoritos / Wishlist)** para clientes logados, com botões de coração interativos nos cards e na PDP com **micro-animações (pulse/bounce)**, tela dedicada `/minha-conta/favoritos` com ação de mover para o carrinho, contador dinâmico na barra de navegação e **feedback visual com Toasts de alta fidelidade**.

---

## 🗄️ 1. Banco de Dados & Modelagem

### Migration: `app/Database/Migrations/2026-08-21-070000_CreateClienteFavoritosTable.php`
- Tabela `cliente_favoritos`:
  - `id` (INT AUTO_INCREMENT PK)
  - `usuario_id` (INT FK -> usuarios.id)
  - `produto_id` (INT FK -> produtos.id)
  - `criado_em` (DATETIME)
  - Índice único `(usuario_id, produto_id)` para prevenir duplicatas.

### Model: `app/Models/ClienteFavoritoModel.php`
- `toggleFavorito(int $usuarioId, int $produtoId): array`: Adiciona ou remove e retorna `['adicionado' => bool, 'total' => int]`.
- `isFavorito(int $usuarioId, int $produtoId): bool`: Verifica se o produto está nos favoritos do usuário.
- `getIdsFavoritosPorUsuario(int $usuarioId): array`: Retorna array simples de IDs favoritados (ex: `[12, 45, 68]`).
- `getFavoritosPorUsuario(int $usuarioId): array`: Retorna produtos completos favoritados (com categoria, preço, imagem e estoque).
- `getTotalFavoritos(int $usuarioId): int`: Contagem rápida de favoritos do usuário.

---

## ⚡ 2. Rotas & Controllers

### Rotas em `app/Config/Routes.php`:
- `post('api/favoritos/toggle', 'FavoritoController::toggle', ['filter' => 'auth'])` (API AJAX para clique no coração)
- `get('api/favoritos/ids', 'FavoritoController::ids')` (Retorna IDs favoritados do usuário logado)
- `get('minha-conta/favoritos', 'ClienteController::favoritos', ['filter' => 'auth'])` (Tela da lista de desejos)
- `post('minha-conta/favoritos/remover/(:num)', 'ClienteController::removerFavorito/$1', ['filter' => 'auth'])`

### Controller: `app/Controllers/FavoritoController.php`
- Endpoint JSON retornando status `ok`, estado booleano do item (`favorito: true/false`), mensagem e totalizador.

### Controller: `app/Controllers/ClienteController.php`
- Adicionar métodos `favoritos()` e `removerFavorito($id)`.

---

## 🎨 3. Front-End, Componentes & Micro-Animações

### Barra de Navegação (`app/Views/layouts/main.php`)
- Botão "Favoritos" no header com ícone de coração e badge de contagem dinâmica (`#badge-favoritos-nav`).

### Cards de Produtos (`app/Views/shop/index.php`, `app/Views/shop/produto_detalhe.php`)
- Botão de favoritar em formato de coração flutuante no canto superior do card do produto:
  - Micro-animação CSS (`heart-bounce`, `scale(1.25)`).
  - Ícone dinâmico (`bi-heart` ➔ `bi-heart-fill text-danger`).
  - Estado otimista instantâneo sem recarregar a página.

### Página "Meus Favoritos" (`app/Views/cliente/favoritos.php`)
- Grid de produtos favoritados dentro do layout Minha Conta (com sidebar ativa).
- Exibição de foto, nome, categoria, preço, status de estoque.
- Ações: "Mover para o Carrinho" e "Remover dos Favoritos".
- Empty state acolhedor quando a lista estiver vazia.

### Sidebar da Conta (`app/Views/cliente/_sidebar.php`)
- Adicionar item "Lista de Desejos" com badge de contagem.

### Toasts Flutuantes de Conversão
- Sistema de notificações Toast no topo/canto da tela para ações rápidas (ex: "Adicionado aos Favoritos", "Produto movido para o carrinho").

---

## 🧪 4. Testes Automatizados

### Arquivo: `tests/app/WishlistEFavoritosTest.php`
1. `testToggleFavoritoAdicionaERemove`: Valida inserção e exclusão atômica de favoritos.
2. `testListarProdutosFavoritosDoUsuario`: Valida que o model retorna produtos enriquecidos com dados de catálogo.
3. `testApiToggleFavoritoRetornaJson`: Valida endpoint `/api/favoritos/toggle`.
4. `testTelaFavoritosRenderizaProdutos`: Valida renderização da view `/minha-conta/favoritos`.

---

## ✅ Critérios de Aceite da Fase 13

1. Usuários logados conseguem favoritar e desfavoritar produtos com 1 clique e animação visual suave na vitrine, PDP e recomendados.
2. Central do cliente exibe a tela de Favoritos com lista de produtos e botão para comprar.
3. Contador da navbar atualiza dinamicamente.
4. Suíte completa de testes passando com 100% de sucesso.
