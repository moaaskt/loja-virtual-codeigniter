# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

E-commerce application built with **CodeIgniter 4** (PHP 8.1+) running on **Docker** (Apache + MySQL 5.7). The stack uses `php:7.4-apache` in the Dockerfile — note the **mismatch**: `composer.json` requires PHP `^8.1` but the Docker image is PHP 7.4. This must be corrected before the app will boot.

## Docker Environment

```bash
# Build and start all containers
docker compose up -d --build

# Install Composer dependencies inside the container (vendor/ does not exist yet)
docker compose exec web composer install

# Run migrations
docker compose exec web php spark migrate

# View PHP/Apache logs
docker compose logs web -f

# Spark CLI (CodeIgniter's artisan equivalent)
docker compose exec web php spark <command>
```

Services:
- `loja_web` → `localhost:8080` (Apache, document root must be `/var/www/html/public`)
- `loja_db` → `localhost:3306` (MySQL 5.7, db: `loja_virtual`, user: `user_loja`, pass: `password_loja`)

## Known Configuration Issues (fix before running)

1. **DocumentRoot is wrong** — The Dockerfile does not set `DocumentRoot /var/www/html/public`. Apache serves from `/var/www/html` directly, exposing `index.php` at root instead of `public/index.php`. Add to Dockerfile:
   ```dockerfile
   RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
   ```

2. **PHP version mismatch** — Image is `php:7.4-apache`; CI4 requires PHP 8.1+. Change to `php:8.1-apache`.

3. **`vendor/` missing** — Run `docker compose exec web composer install` after first build.

4. **DB volume path typo** — `docker-compose.yml` has `/var/var/lib/mysql` instead of `/var/lib/mysql`.

5. **`indexPage` not blank** — `app/Config/App.php` has `$indexPage = 'index.php'`. Set to `''` for clean URLs via mod_rewrite.

6. **No `.env` file** — Database credentials are hardcoded in `app/Config/Database.php` (hostname `db`, user `user_loja`, db `loja_virtual`). This is intentional for Docker; no `.env` is needed unless you want to override per environment.

7. **`writable/` permissions** — After container starts: `docker compose exec web chown -R www-data:www-data /var/www/html/writable`

## Architecture

### Request Lifecycle
All requests enter `public/index.php` → CI4 bootstrap → `app/Config/Routes.php` → Controller → Model → View.

### Authentication & Authorization
Session-based auth. Two custom filters in `app/Filters/`:
- `Auth` — checks `session()->get('isLoggedIn')`, redirects to `/login` if missing.
- `Admin` — checks `session()->get('role') === 'admin'`, redirects to `/` if not admin.

Filters are registered as `auth` and `admin` aliases in `app/Config/Filters.php` and applied per-route in `Routes.php`.

### Route Groups
- **Public shop** — `/`, `/produto/:id`, `/categoria/:id`, `/busca`, `/carrinho/*`
- **Auth** — `/login`, `/registrar`, `/logout`
- **Cliente (auth filter)** — `/minha-conta/pedidos`, `/checkout/finalizar`, `/pedido/sucesso`
- **Admin (auth + admin filters)** — `/admin/dashboard`, `/admin/pedidos/*`, `/admin/categorias/*`, `/admin/produtos/*`

### Controllers
- `HomeController` — shop front: product listing, search, detail pages
- `CarrinhoController` — cart CRUD (session-based cart)
- `AuthController` — login/register/logout
- `ClienteController` — customer order history
- `PedidoController` — checkout finalization
- `Admin\AdminController` — dashboard
- `Admin\PedidoController` — order management + status updates
- `Admin\CategoriasController` / `Admin\ProdutosController` — CRUD for catalog

### Views
Two layouts in `app/Views/layouts/`: `main.php` (shop) and `admin.php` (back-office). Views are organized by domain: `shop/`, `auth/`, `admin/`, `cliente/`.

### Models
- `ProdutoModel`, `CategoriaModel` — catalog
- `UsuarioModel` — users/auth
- `PedidoModel`, `PedidoProdutoModel` — orders and order items

### Database
No `.sql` seed file exists in the repo. Schema must be created via CI4 migrations (`php spark migrate`). If migrations don't exist yet, the schema must be created manually.

### File Uploads
Product images upload to `public/uploads/` (accessible via Apache directly).

### Helper
`app/Helpers/status_helper.php` — shared helper for order status labels; auto-loaded or called via `helper('status')`.
