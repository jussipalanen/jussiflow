# JussiFlow

A demo application for **financial management, invoicing and accounting**, aimed at end users.

[![CI](https://github.com/jussipalanen/jussiflow/actions/workflows/ci.yml/badge.svg)](https://github.com/jussipalanen/jussiflow/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4.svg?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![CakePHP](https://img.shields.io/badge/CakePHP-5.4-D33C43.svg?style=flat-square)](https://cakephp.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4-38BDF8.svg?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

---

## <img src="webroot/img/cake.icon.png" width="20" height="20" alt=""> Powered by the CakePHP Framework

<a href="https://cakephp.org"><img src="webroot/img/cake.power.gif" width="98" height="13" alt="Powered by CakePHP"></a>

JussiFlow is built on **[CakePHP](https://cakephp.org) 5.4** — the rapid development framework
for PHP. Nearly everything below leans on what CakePHP gives you out of the box:

| CakePHP feature | Role in JussiFlow | Status |
|---|---|---|
| **Server-rendered templates** | Every screen is a `.php` template; no JSON API, no client-side framework | In use |
| **CSRF middleware** | Enabled application-wide in `src/Application.php` | In use |
| **Routing** | `/pages/*` renders templates with no controller code of its own | In use |
| **Convention-based ORM** | Table and entity classes resolve from table names, no mapping config | Planned |
| **`bake` scaffolding** | Will generate the invoice CRUD screens from the database schema | Planned |
| **Migrations** | Schema in version control, applied with one command | Planned |
| **`DefaultPasswordHasher`** | Password hashing on the `User` entity | Planned |

The framework source lives at [cakephp/cakephp](https://github.com/cakephp/cakephp), and the
documentation is the [CakePHP 5 Book](https://book.cakephp.org/5/en/).

---

## What it does

The scope is deliberately small:

- Users log in with their own credentials
- Users manage their own details from a profile page
- Users create, read, update and delete invoices

It is a demo, not a production accounting system.

> **Current state.** Early days. The sign-in and password-reset screens exist as templates, but
> there is no authentication behind them yet — and no models, migrations or invoice code. The
> forms render and submit; they do not yet do anything. Check before assuming the domain exists.

## Stack

| Layer | Choice |
|---|---|
| Language | PHP 8.5 |
| Framework | CakePHP 5.4 |
| Database | MariaDB 11.4 in Docker (application); SQLite (test suite) |
| Interface | Server-rendered CakePHP templates |
| Styling | Tailwind CSS 4, compiled by the standalone CLI |
| Tests | PHPUnit 13 |
| Static analysis | PHPStan level 8 |
| Linting | PHP_CodeSniffer 4, `CakePHP` standard |

## Getting started

### With Docker (recommended)

```bash
./dev up        # start (builds on first run)
./dev logs      # follow the logs
./dev down      # stop and remove
```

The app is then at **http://localhost:8765**.

Run commands inside the container:

```bash
docker compose exec app bin/cake migrations migrate
docker compose exec app bin/cake bake all Invoices
```

### Without Docker

```bash
composer install
cp config/app_local.example.php config/app_local.php
bin/cake server -p 8765
```

> **Never run `bin/cake` with `sudo`.** It does not need root, and doing so leaves root-owned
> files in `tmp/` and `logs/` that your normal user can no longer write.

### Screens

| Screen | URL |
|---|---|
| Home | `/` |
| Sign in | `/pages/login` |
| Forgot password | `/pages/forgot-password` |

## Styling

Tailwind CSS 4, compiled by the **standalone CLI** — a single binary, so the PHP-only image
needs no Node or npm and the pages pull nothing from a CDN at runtime.

```bash
./dev css            # compile once
./dev css --watch    # recompile while editing templates
```

- `resources/css/app.css` is the **source** — theme tokens, fonts, component classes.
- `webroot/css/app.css` is the **compiled output**, committed so a fresh clone renders with no
  build step. Never edit it by hand.

Tailwind only emits classes it can find in the templates, so **re-run `./dev css` after adding
one** or it will not exist in the stylesheet.

## Testing

```bash
./dev test           # PHPUnit in the container
composer test        # PHPUnit locally
composer cs-check    # lint
composer cs-fix      # autofix lint
```

The test suite uses its own SQLite database and is safe to run repeatedly — it needs no
database server, unlike the application itself, which runs on the MariaDB container.

## Project layout

```
config/       routes, bootstrap, app configuration
resources/    Tailwind source (compiles into webroot/css)
src/          application code — controllers, models, middleware
templates/    server-rendered views, one file per action
tests/        PHPUnit tests, mirroring src/
webroot/      document root: compiled CSS, fonts, images
```

## Contributing

Read **[AGENTS.md](AGENTS.md)** first. It records the settled architecture decisions — money is
stored as integer cents, queries are scoped to the logged-in user — along with the conventions
`bake` and the ORM depend on.

## Credits

Built with the [CakePHP Framework](https://cakephp.org). CakePHP is released under the
[MIT License](https://github.com/cakephp/cakephp/blob/5.x/LICENSE), and the CakePHP name and
logo are trademarks of the [Cake Software Foundation](https://cakefoundation.org).
