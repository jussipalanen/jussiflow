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
| Interface | Server-rendered CakePHP templates (Milligram CSS) |
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
```

**Never run `bin/cake` with `sudo`.** It does not need root, and running as root leaves
root-owned files in `tmp/` and `logs/` that your normal user can no longer write.

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
