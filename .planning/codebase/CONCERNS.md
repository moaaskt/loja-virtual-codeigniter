# Concerns & Issues

## Current Known Issues

1. **PHP Version Mismatch in Docker**:
   - The `composer.json` mandates PHP `^8.1`.
   - The `Dockerfile` currently pulls `php:7.4-apache`.
   - *Fix Needed*: Update Dockerfile to `FROM php:8.1-apache`.

2. **Apache DocumentRoot Misconfiguration**:
   - The Apache container serves from `/var/www/html` by default, exposing `index.php` at the root folder level rather than within `public/`.
   - *Fix Needed*: Add `RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf` to the Dockerfile.

3. **Missing Vendor Directory**:
   - The `vendor/` directory is not installed via the Dockerfile build process.
   - *Workaround*: Must run `docker compose exec web composer install` manually after bringing containers up.

4. **Docker Compose DB Volume Typo**:
   - `docker-compose.yml` mounts the database volume to `/var/var/lib/mysql` instead of `/var/lib/mysql`.
   - *Fix Needed*: Correct the path in `docker-compose.yml`.

5. **CodeIgniter indexPage Configuration**:
   - `app/Config/App.php` has `$indexPage = 'index.php'`. This should be changed to `''` for clean URLs using mod_rewrite.

6. **Database Credentials**:
   - Hardcoded in `app/Config/Database.php` instead of using a `.env` file. While intentional for Docker, this can cause friction if moving to other environments.

7. **Permissions**:
   - The `writable/` folder lacks correct permissions upon container start.
   - *Workaround*: Run `docker compose exec web chown -R www-data:www-data /var/www/html/writable` manually.
