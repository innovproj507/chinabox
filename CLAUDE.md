# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

ChinaBox is a Spanish-language PHP invoicing/logistics system ("Sistema de Facturación") for a courier/shipping business in Panama. It manages clients, services (shipping types), and invoices ("facturas"), with printing (direct-to-printer and PDF), Excel export, and WhatsApp notification integration. There is no test suite, build step, or JS framework — it's server-rendered PHP with vanilla JS in views.

The user auth table (`auth_user`) and its `pbkdf2_sha256` password hashes indicate this app's user/auth data was migrated from a prior Django application.

## Environment & running the app

- PHP 8.2+, MySQL, served via Laragon on Windows (local dev URL configured in `.env` as `APP_URL`, e.g. `https://chinabox.test/`).
- Dependencies are managed with Composer (`composer install`). Key libraries: `vlucas/phpdotenv`, `dompdf/dompdf` (PDF generation), `phpoffice/phpspreadsheet` (Excel export).
- Config lives in `.env` (DB credentials, `APP_URL`, `APP_ENV`, and Windows-specific direct-print settings `DIRECT_PRINT_ENABLED` / `PRINTER_NAME`).
- No test suite, linter, or build tooling is configured — there are no `phpunit`, `composer test`, or `npm` scripts to run. Verify changes by exercising routes directly in the browser against the local Laragon vhost.
- `get_invoice.php` is a standalone debug script (run via `php get_invoice.php`) that prints the latest invoice ID directly from the DB, bypassing the app.

## Architecture

Custom, dependency-free MVC framework (no Laravel/Symfony) under `App\` (PSR-4 → `src/`):

- **Entry point**: [index.php](index.php) loads Composer autoload, `.env` via phpdotenv, starts the session (`App\Core\Session`), builds an `App\Core\Router`, includes [src/routes.php](src/routes.php) to register routes, then dispatches on `$_SERVER['REQUEST_URI']`.
- **Routing** ([src/Core/Router.php](src/Core/Router.php)): routes are registered as `$router->get(path, ControllerClassName, methodName)` / `->post(...)` in [src/routes.php](src/routes.php). `{param}` segments become regex capture groups and are passed positionally to the controller method. The router strips the base path using `SCRIPT_NAME` (supports running from a subdirectory) and rewrites via `.htaccess` (`RewriteRule ^(.*)$ index.php`) so all requests funnel through `index.php`. No route-level middleware — auth checks happen per-controller.
- **Controllers** (`src/Controllers/`): plain classes instantiated fresh per request; no DI container. Auth-gated controllers check `Auth::check()` in their constructor and redirect to `/` if not logged in (see [FacturaController.php](src/Controllers/FacturaController.php)). Controllers mix concerns freely — a single action often handles both the GET (render form) and POST (process + `json_encode` or redirect) cases via `Request::isPost()`, and JSON API-style endpoints and normal page renders coexist in the same class.
- **Models** (`src/Models/`): extend `App\Models\BaseModel` ([src/Models/BaseModel.php](src/Models/BaseModel.php)), which provides generic `find`, `all`, `where`, `create`, `update`, `delete`, `count` over a `$table`/`$primaryKey` using raw PDO with bound parameters. Subclasses add hand-written queries (joins, filters, pagination) as needed — there is no query builder or relationship/ORM layer.
- **Database** ([src/Core/Database.php](src/Core/Database.php)): singleton PDO connection, config pulled from `.env`/`$_SERVER`/`getenv()` fallback chain (needed because Laragon's `variables_order` sometimes doesn't populate `$_ENV`). Always prefer prepared statements when adding queries.
- **Views** (`views/`): plain PHP templates rendered via `App\Core\View::render($viewPath, $data)`, which `extract()`s `$data`, captures output via `ob_start()`, and wraps it in `views/layout/base.php` unless the view sets `$useBase = false` (used for `login`, `imprimir`, `pdf_server` — standalone pages). Views are organized by resource (`views/facturas/`, `views/clientes/`, `views/servicios/`, `views/configuracion/`), plus shared partials in `views/includes/`.
- **Session/Flash**: `App\Core\Session` wraps `$_SESSION` with security-conscious cookie params. `View::render` pre-loads flash messages and calls `session_write_close()` before rendering so the session isn't held open during long template rendering; `App\Core\Response::redirect()` similarly closes the session before issuing a `Location` header. Keep this ordering in mind when touching session/flash code — reading session data after `session_write_close()` in the same request will not work as expected.
- **Auth** ([src/Core/Auth.php](src/Core/Auth.php)): custom login against `auth_user` table, verifying Django-format `pbkdf2_sha256$iterations$salt$hash` passwords manually (`hash_pbkdf2`). There is no user-facing registration; users are managed via `ConfiguracionController` (`/configuracion/usuarios`).

## Domain-specific logic worth knowing before editing

- **Invoice numbering**: codes are generated as `{MES}{AÑO}-{consecutivo}` (e.g. `ENE2026-0001`) via `ConsecutivoModel::getNextConsecutivo()`, scoped per month/year. Don't hardcode number formats elsewhere.
- **Invoice totals**: `subtotal` is computed from line items (`precio_unitario * peso`), then `descuento` is subtracted, then ITBMS tax is applied conditionally (`aplica_itbms` checkbox) using the percentage from `ConfiguracionModel::getConfig()`, giving `total`. This calculation is duplicated in `FacturaController::crear()` and should be mirrored carefully if edited in `editar()` or elsewhere.
- **Paid invoices are locked**: once `factura.estado == 'Pagada'`, edit/delete/anular actions are blocked in the controller (not just the UI) — preserve this check when modifying those flows.
- **PDF/printing has three modes**, all in `FacturaController::imprimir()`/`pdf()`: (1) browser preview via `views/facturas/imprimir.php`, (2) server-side PDF via Dompdf + `views/facturas/pdf_server.php` (used for the `/pdf` route and for direct printing), (3) Windows-only **direct-to-printer** flow that renders a PDF, writes it to a temp file, and shells out to `bin/SumatraPDF.exe -print-to` — gated by `.env`'s `DIRECT_PRINT_ENABLED`/`PRINTER_NAME`. This direct-print path only works on the Windows/Laragon host it was built for.
- **WhatsApp integration** (`FacturaController::abrirApp`): shells out to a local WhatsApp desktop executable (path from system config) or the `whatsapp://` URI scheme, with message text branched by service type (`esMaritimo`, `soloFoto` service codes `O1`-`O6`). Phone numbers are normalized to Panama's `507` prefix.
- **Excel export** (`FacturaController::exportar`, and similar in `ClienteController`): builds an `.xlsx` in a temp file via PhpSpreadsheet and streams it, rather than writing directly to output — keep this pattern (`tempnam` + `readfile` + `unlink`) for consistency and to avoid corrupt downloads from stray output buffering.
- **`.phpbk` files** (e.g. `FacturaController.phpbk`, `routes.phpbk`, several views) are manual backups left in place next to their live counterparts — not part of the autoloaded/executed app. Don't treat them as dead code to silently delete without confirming with the user, but also don't edit them expecting effect.
