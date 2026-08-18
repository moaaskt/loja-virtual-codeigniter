# Project Structure

## Root Directories
- `app/`: The core application code (MVC components, configuration, filters, helpers).
- `public/`: The document root for the web server. Contains the `index.php` entry point, uploaded files (`uploads/`), and frontend assets.
- `tests/`: Automated tests and support files for PHPUnit.
- `vendor/`: Composer dependencies (created after running `composer install`).
- `writable/`: Directory used by CodeIgniter for caching, logs, and session data. Needs write permissions.

## App Directory Details
- `app/Config/`: Configuration files (e.g., `Routes.php`, `App.php`, `Database.php`, `Filters.php`).
- `app/Controllers/`: Application controllers, split into root and `Admin/` namespaces.
- `app/Filters/`: Middleware classes (`Auth`, `Admin`).
- `app/Helpers/`: Custom helper functions (e.g., `status_helper.php` for order statuses).
- `app/Models/`: Database models extending CI4's Model class.
- `app/Views/`: UI templates, organized into layouts and domain-specific folders (`shop/`, `auth/`, `admin/`, `cliente/`).
