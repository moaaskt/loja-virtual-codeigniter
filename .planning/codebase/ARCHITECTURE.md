# Architecture

## Pattern
The application follows the traditional **MVC (Model-View-Controller)** pattern provided by CodeIgniter 4.

## Request Lifecycle
1. All requests enter via `public/index.php`.
2. CI4 bootstrap initializes the framework.
3. Routing is handled via `app/Config/Routes.php`.
4. Route filters (middleware) are applied for authentication/authorization.
5. The request reaches the designated Controller.
6. The Controller interacts with Models for data retrieval/persistence.
7. The Controller passes data to the View for rendering.

## Authentication & Authorization
- **Type**: Session-based authentication.
- **Filters**: Two main filters reside in `app/Filters/`:
  - `Auth`: Ensures the user is logged in (`session()->get('isLoggedIn')`).
  - `Admin`: Ensures the logged-in user has the admin role (`session()->get('role') === 'admin'`).

## Controllers
- `HomeController`: Shop frontend (product listing, search, detail pages).
- `CarrinhoController`: Cart CRUD (using session-based cart).
- `AuthController`: Login, register, logout logic.
- `ClienteController`: Customer area and order history.
- `PedidoController`: Checkout finalization.
- `Admin\AdminController`: Admin dashboard.
- `Admin\PedidoController`: Admin order management.
- `Admin\CategoriasController` & `Admin\ProdutosController`: Catalog CRUD.

## Models
- **Catalog**: `ProdutoModel`, `CategoriaModel`
- **Users**: `UsuarioModel`
- **Orders**: `PedidoModel`, `PedidoProdutoModel`
