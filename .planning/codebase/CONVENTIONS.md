# Conventions

## Namespaces
Code follows CodeIgniter 4 PSR-4 autoloading standards.
- Application code uses the `App\` namespace.
- Admin controllers are placed under the `App\Controllers\Admin` namespace.

## Route Groups
Routes in `app/Config/Routes.php` should be grouped logically:
- Public shop routes (`/`, `/produto/:id`, `/busca`, `/carrinho/*`).
- Authentication routes (`/login`, `/registrar`, `/logout`).
- Authenticated user routes (filtered by `auth`) (`/minha-conta/*`, `/checkout/*`).
- Admin routes (filtered by `auth` and `admin`) (`/admin/*`).

## Views Organization
Views are organized strictly by domain:
- `app/Views/layouts/`: Base HTML structures (e.g., `main.php` and `admin.php`).
- `app/Views/shop/`: Public shop pages.
- `app/Views/auth/`: Login and registration pages.
- `app/Views/admin/`: Backend dashboard and management pages.
- `app/Views/cliente/`: Customer account pages.

## Database Migrations
Database schema should be managed via CodeIgniter migrations, executed using `php spark migrate`. Seed files or manual `.sql` files are discouraged as primary schema sources.
