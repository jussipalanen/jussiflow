# JussiFlow

Guidance for AI agents and developers working in this repository.

## What this project is

JussiFlow is a **demo application for financial management, invoicing and accounting**, aimed at end users. The scope is deliberately bounded:

- Users log in with their own credentials
- Users manage their own details from a profile page
- Users create, read, update and delete invoices

It is a demo, not a production accounting system. Prefer clarity and correctness over feature breadth.

## Stack

| Layer     | Choice |
|-----------|--------|
| Language  | PHP 8.5.4 |
| Framework | CakePHP 5.4.1 |
| Database  | SQLite (development and test) |
| Interface | Server-rendered CakePHP templates (Tailwind CSS 4) |
| Tests     | PHPUnit 13.3.2 |
| Linting   | PHP_CodeSniffer 4 with the `CakePHP` standard |

## Current state — read this before assuming anything exists

As of the last update to this file, the repository is a **stock CakePHP 5.4 skeleton**. The only
application-specific code is:

- `src/Middleware/HostHeaderMiddleware.php` — rejects mismatched `Host` headers in production,
  guarding against Host Header Injection (poisoned password-reset links). Registered in
  `src/Application.php`. Note it **returns early when `debug` is true**, so it is inert in
  development and only engages in production.

There are **no** models, entities, tables, migrations, auth, or invoice code yet. Do not assume
the domain exists — check before referencing it.

## Commands

```bash
composer install          # install dependencies
bin/cake server -p 8765   # dev server -> http://localhost:8765
composer test             # PHPUnit
composer cs-check         # lint
composer cs-fix           # autofix lint
bin/cake bake all Invoices    # scaffold model+controller+templates from schema
bin/cake migrations migrate   # apply migrations
./dev css                     # compile the Tailwind theme (--watch while editing templates)
```

**Never run `bin/cake` with `sudo`.** It does not need root, and running as root leaves
root-owned files in `tmp/` and `logs/` that your normal user can no longer write.

## Running with Docker

Use the `./dev` helper script:

```bash
./dev up        # start (builds on first run)
./dev down      # stop and remove
./dev restart   # down, then up
./dev logs      # follow logs
```

The app is then at **http://localhost:8765**.

Running commands inside the container:

```bash
docker compose exec app bin/cake migrations migrate
docker compose exec app bin/cake bake all Invoices
docker compose exec app composer test
```

How the setup is put together:

- **`Dockerfile`** — three stages. The first is a `php:8.5-apache` base with `intl` (required by
  CakePHP) and `zip` built in, plus the Composer binary. The second resolves Composer dependencies
  in isolation so that layer only rebuilds when `composer.json`/`composer.lock` change. The third
  is the application runtime. `mbstring`, `pdo_sqlite`, `dom`, `xml` and `opcache` already ship in
  the base image.

  Both the dependency and runtime stages derive from the *same* base on purpose. Resolving the
  lock file on a stock `composer` image fails, because that image has neither `ext-intl` nor
  PHP 8.5, and `cakephp/cakephp` requires `ext-intl`. Do not add `opcache` to
  `docker-php-ext-install` either — it is compiled into `php:8.5` and enabled by default, so
  building it produces no `.so` and `make install` dies on `cp: cannot stat 'modules/*'`.
- **`docker/apache/vhost.conf`** — `DocumentRoot` is `webroot/`, so `src/`, `config/` and
  `vendor/` are not reachable over HTTP. `AllowOverride All` is required for CakePHP's
  `.htaccess` rewrite rules.
- **`docker/entrypoint.sh`** — self-healing startup: runs `composer install` if `vendor/` is
  missing, falls back to the env-driven config if `config/app_local.php` is absent, creates the
  writable `tmp/`, `logs/` and `data/` directories, and warns on an unset `SECURITY_SALT`.
- **`docker/app_local.php`** — env-driven configuration used inside the container, since the real
  `config/app_local.php` is gitignored and never enters the image.
- **`dev`** — thin wrapper over `docker compose` that exports `HOST_UID`/`HOST_GID` and probes
  for a working compose binary before use.

Two details worth knowing:

- **`HOST_UID`/`HOST_GID`.** The container's `www-data` is remapped to your host UID so that
  bind-mounted files stay writable. `./dev` exports these for you. They are deliberately *not*
  named `UID`/`GID`: bash marks `UID` readonly and never exports it, so `UID=$(id -u) docker
  compose up` fails outright and `${UID}` in compose would arrive empty. If you invoke compose
  by hand, pass `HOST_UID=$(id -u) HOST_GID=$(id -g)` — otherwise a host user that is not `1000`
  will hit permission errors writing to `tmp/` and `logs/`.
- **The salt in `docker-compose.yml` is a development value.** Generate a real one with
  `openssl rand -hex 32` before deploying anywhere that matters.

## Styling

Tailwind CSS 4, compiled by the **standalone CLI** — a single binary, so the PHP-only Docker
image needs no Node or npm, and the page pulls no CDN at runtime.

- `resources/css/app.css` is the **source**: theme tokens, `@font-face`, and the handful of
  `@layer components` classes. Edit this one.
- `webroot/css/app.css` is the **compiled output** and is committed, so a fresh clone renders
  correctly without running any build. Never edit it by hand; `./dev css` overwrites it.
- `./dev css` fetches the pinned Tailwind binary into `tmp/` on first use. Add `--watch` while
  working on templates.

**Rebuild after changing templates.** Tailwind only emits classes it can find in the files listed
by the `@source` directives, so a new utility class in a template does nothing until you re-run
`./dev css`.

The palette is two families, both defined in `@theme`: `navy-*` (dark blue — headings, buttons,
focus rings) and `cream-*` (the light page ground). Prefer these over Tailwind's stock `blue-*`
or `slate-*` so the theme stays in one place.

Only `.field-label`, `.field-input` and `.btn-primary` are promoted out of utilities, because they
repeat verbatim across every form. Everything else stays as utilities in the markup.

**Watch out for dynamically built class names.** Tailwind scans for literal strings, so
`"badge-{$invoice->status}"` will be purged. Write the full class name out (as
`templates/element/flash/*.php` does) or safelist it.

## Architecture decisions

These are settled. Do not silently revisit them.

### 1. Money is stored as integer minor units (cents)

**Never use floats or PHP floats for monetary values.** `0.1 + 0.2 !== 0.3` in binary floating
point, and in an accounting context those errors accumulate into totals that do not reconcile.

- Store amounts as `INTEGER` cents (e.g. `12345` means 123.45)
- Name such columns with a clear suffix, e.g. `unit_price_cents`, `total_cents`
- Convert to a display string only at the view layer
- Compute totals with integer arithmetic

This is cheap to honour now and painful to retrofit once data and totals logic exist.

### 2. SQLite for development and test

`pdo_sqlite` is installed and no database server is required. The `test` datasource in
`config/app_local.php` already defaults to `sqlite://127.0.0.1/tmp/tests.sqlite`.

Because money is stored as integers, SQLite's loose numeric typing costs nothing here.

### 3. Server-rendered, not a JSON API

This app renders its own HTML via CakePHP templates rather than exposing a JSON API. `bake` generates
working CRUD screens from the schema — use it rather than hand-writing boilerplate controllers
and templates.

### 4. Planned schema (not yet implemented)

```
users         id, email, password, first_name, last_name, created, modified
clients       id, user_id, name, email, address, created, modified
invoices      id, user_id, client_id, invoice_number, issue_date, due_date,
              status, subtotal_cents, tax_total_cents, total_cents, notes,
              created, modified
invoice_items id, invoice_id, description, quantity, unit_price_cents,
              tax_rate, line_total_cents
```

Invoice totals are derived from `invoice_items`, not entered directly.

## Security rules

This application handles money and has user accounts. These are not optional.

1. **Scope every query to the logged-in user.** The single most likely bug in an app of this
   shape is broken ownership scoping — user A loading user B's invoice by changing the ID in the
   URL. Every finder for invoices, invoice items and clients must filter on the authenticated
   `user_id`. Never trust an ID from the request on its own.
2. **Hash passwords** with CakePHP's `DefaultPasswordHasher`, set from a `_setPassword()` mutator
   on the User entity so plaintext never reaches the database.
3. **CSRF protection is enabled** in `src/Application.php`. Do not disable it for convenience.
4. **Never commit secrets.** `config/app_local.php` holds the security salt and is gitignored.

Authentication uses the **`cakephp/authentication` plugin** (`composer require cakephp/authentication`).
The old `AuthComponent` was removed in CakePHP 4 and does not exist in 5.x.

## Conventions

Follow CakePHP naming conventions — `bake` and the ORM depend on them:

- Table class `InvoicesTable`, entity `Invoice`, controller `InvoicesController`
- Database tables plural and snake_case (`invoice_items`); foreign keys `<singular>_id`
- Templates in `templates/Invoices/`, one file per action
- All PHP files use `declare(strict_types=1);`
- Code must pass `composer cs-check` (CakePHP coding standard)

## Testing

- Tests live in `tests/TestCase/`, mirroring `src/`
- Use fixtures in `tests/Fixture/` for model tests
- The test suite uses its own SQLite database and is safe to run repeatedly
- Run `composer test` before considering work complete

## Gotchas

- `vendor/` is not committed; run `composer install` on a fresh clone
- `config/app_local.php` is gitignored — create it from `config/app_local.example.php`
- DebugKit only loads when `debug` is true, and stores its panel data in a SQLite file in `tmp/`
- `HostHeaderMiddleware` is skipped entirely in debug mode, so it is untested locally by design
