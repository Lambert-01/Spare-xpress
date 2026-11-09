# Spare Xpress

Spare Xpress is a PHP and MySQL web application for Spare Xpress Ltd, an automotive parts business in Rwanda. Customers can browse parts by vehicle, submit special-order requests, place orders, and manage their accounts. Staff use the admin area to maintain the catalog and process customer requests and orders.

This guide is for developers joining the project. It covers the code layout, local setup, and the checks to make before changing or deploying the application.

## What the application does

- Lists parts by vehicle brand, model, year, and category, with product details and images.
- Supports customer accounts, a shopping cart, checkout, order history, and order status views.
- Accepts special-order and price requests for parts that are not listed as available.
- Provides customer messaging and contact forms.
- Gives staff an admin dashboard for products, brands, models, categories, customers, orders, requests, notifications, and inventory exports.
- Creates invoices and sends transactional email when the required services are configured.

Payment options shown in the site and the order workflow should not be assumed to mean that an online payment gateway is connected. Check the current flow and provider configuration before changing payment behavior.

## Technology

- PHP 8 or newer, using MySQLi for database access.
- MySQL or MariaDB.
- Composer packages: PHPMailer, PhpSpreadsheet, and TCPDF.
- Browser UI built with PHP templates, HTML, CSS/SCSS, and JavaScript.
- Apache rewrite/security rules in `.htaccess`; an Nginx configuration example is in `nginx-seo.conf`.

`composer.lock` is currently excluded by `.gitignore`. A fresh checkout may therefore resolve different compatible dependency versions when `composer install` runs; coordinate dependency changes and check for compatibility issues.

## Project layout

| Path | Purpose |
| --- | --- |
| `index.php` | Public storefront home page. |
| `pages/` | Customer pages such as shop, product details, login, cart, checkout, orders, and special requests. |
| `api/` | JSON and form endpoints used by the customer site. |
| `admin/` | Staff dashboard and catalog, customer, order, and request management. `admin/api/` contains admin-side endpoints. |
| `includes/` | Shared configuration, environment loading, sessions, navigation, email, invoices, and other shared code. |
| `sql/` | Database schema and SQL data/import scripts. |
| `css/`, `scss/`, `js/`, `img/`, `lib/` | Styles, frontend code, images, and bundled frontend libraries. |
| `uploads/` | User and catalog uploads. Treat uploaded files as runtime data and preserve them when maintaining an installation. |
| `.env.example` | Template for local and deployment environment settings. |
| `DEPLOYMENT.md` | Environment-specific production deployment instructions. |
| `PROJECT_BRIEF.md`, `PROJECT_DESCRIPTION.md` | Additional product and architecture background. |

The database contains both older and newer table names; many newer tables use an `_enhanced` suffix. Check the SQL schema and the code used by the page you are changing before adding queries or altering data.

## Local development setup

You need PHP 8+, Composer, and a local MySQL or MariaDB server. PHP must have the `mysqli` extension enabled. Composer or PHP may report additional extensions needed by spreadsheet or PDF features.

1. Open a terminal in the repository root and install PHP dependencies:

   ```sh
   composer install
   ```

2. Create a **dedicated local** database. For example, in the MySQL client:

   ```sql
   CREATE DATABASE sparexpress_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Review `sql/sparedb.sql` before importing it. It contains both schema and records. Import it only into the empty local database you created:

   ```sh
   mysql -u root -p sparexpress_local < sql/sparedb.sql
   ```

   If your local database user or client command differs, adjust the command accordingly. Never import this dump into a shared or production database.

4. Copy `.env.example` to `.env` and set the local database values. `.env` is ignored by Git; keep credentials and API keys there, never in committed source files.

   ```env
   SITE_URL="http://127.0.0.1:8000"
   DB_HOST="127.0.0.1"
   DB_PORT="3306"
   DB_NAME="sparexpress_local"
   DB_USER="root"
   DB_PASS="your-local-password"
   ```

   `includes/env.php` reads `.env`, and `includes/config.php` opens the MySQL connection. Optional mail, Cloudinary, and Google OAuth settings are also documented in `.env.example`; leave them unset for work that does not need those services.

5. Start PHP's built-in development server from the repository root:

   ```sh
   php -S 127.0.0.1:8000
   ```

   Visit `http://127.0.0.1:8000/`. PHP's built-in server does not apply the Apache rules in `.htaccess`; use the direct `.php` paths locally when extensionless routes or rewrite behavior are needed.

   The staff area is under `/admin/`. It requires a local admin account; the project does not document a default login for collaborators.

The SQL dump includes records as well as table definitions. It may contain old or example account data, and no default admin login is documented here. Use only a local database and establish local admin access through the project's approved process.

## Configuration and integrations

The main settings are in `.env.example`. Common groups are:

- Site identity and contact details.
- MySQL connection details (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) or a MySQL `DATABASE_URL`.
- SMTP or Resend settings for email.
- Cloudinary credentials for hosted image uploads.
- Google OAuth client and callback settings.

Database access currently uses MySQLi. A PostgreSQL URL is not supported by this codebase. Keep secrets out of source control, logs, screenshots, and issue reports. If a credential is exposed, tell the project maintainer so it can be rotated.

## Making a change

1. Start from the current shared branch and create a focused branch for your work.
2. Trace the full flow before editing: page/template, JavaScript request, API handler, and the database tables it reads or writes.
3. Keep database changes in a clearly named SQL script under `sql/`, and explain how it should be applied. Back up local data before testing schema changes.
4. Keep credentials, local `.env`, logs, generated invoices, and uploaded/runtime data out of commits. Review `git status` and the staged diff before committing.
5. For PHP edits, run `php -l path/to/changed-file.php` on each changed PHP file. Then exercise the affected flow locally, including both a normal case and a failure case where practical.
6. In your handoff or pull request, describe the user-visible change, database/configuration changes, and checks you actually ran. Do not report an unrun check as passed.

There is no project test command defined in `composer.json`. Coordinate any broader manual checks with the maintainer, especially for orders, payments, customer records, email, and production data.

## Areas that need careful review

- Checkout totals and stock changes affect customer charges and inventory. Recalculate prices and quantities from trusted server/database values; do not rely on values supplied by the browser.
- Validate uploaded files by content and allowed type, and ensure uploads cannot execute as scripts.
- Protect state-changing customer and admin actions against cross-site request forgery, and preserve the existing session/authentication boundaries.
- The schema has legacy and enhanced tables. A change to one table may not affect every page that still reads another table.
- Keep order creation and related inventory writes consistent if any part of the transaction fails.

These are review priorities for future work, not a claim that every flow has been fully audited.

## Deployment

Production deployment is restricted to authorized maintainers. Read [DEPLOYMENT.md](DEPLOYMENT.md) for the environment-specific process, and coordinate with the maintainer before using it. Do not copy production credentials into local configuration, and do not replace production database contents as part of routine code deployment.

## Additional project notes

- `PROJECT_BRIEF.md` gives a shorter product summary.
- `PROJECT_DESCRIPTION.md` contains a longer architecture and feature description; verify claims against the current source before relying on them.
- `DEPLOYMENT.md` contains server-specific operational details and should be handled as maintainer documentation.
