# Laravel Portfolio

A personal portfolio site with a built-in admin CMS. Pages, navigation, and content are managed from an admin dashboard instead of being hard-coded, and a contact form delivers messages through the Gmail API.

Built with **Laravel**, **Inertia.js**, **Vue 3**, and **Tailwind CSS**, and developed inside a Docker Compose environment (Apache + PHP 8.4, PostgreSQL 15).

## Features

- **Dynamic pages**: create and edit pages in the admin area; any published slug is served by a catch-all route.
- **WYSIWYG editing**: rich-text page content via a Tiptap-based editor (links and images supported).
- **Navigation manager**: build nested menus, link to pages or external URLs, and drag to reorder.
- **Contact form**: rate-limited (5 requests/minute), honeypot spam protection, and delivery through the Gmail API using OAuth.
- **Admin dashboard**: protected by `auth` + `admin` middleware (`is_admin` flag on users).
- **Read-only database viewer**: browse tables from the admin area (GET-only by design).
- **Authentication**: Laravel Fortify login, profile and security settings, and passkey/two-factor scaffolding.
- **Idempotent super-admin seeder**: creates or updates the site owner from environment variables, safe to re-run on every deploy.
- **Split-by-level logging**: a custom log channel writes each level to its own rotating file, plus a mailable for exception notifications.
- **SEO fields per page**: SEO title, meta description, and keywords editable in the admin, rendered as `<title>`, meta, Open Graph, and canonical tags.
- **Server-side rendering**: public pages are pre-rendered by Inertia SSR for search engines, link scrapers, and faster first paint; the admin stays client-rendered.
- **Tooling**: PHPUnit, Larastan (PHPStan), Laravel Pint, Xdebug pre-configured in the container.

## Tech Stack

| Layer      | Technology                                                  |
| ---------- | ----------------------------------------------------------- |
| Backend    | PHP 8.3+ (container runs 8.4), Laravel 13, Fortify          |
| Frontend   | Vue 3, Inertia.js 3 (with SSR), Ziggy, Tailwind CSS 3, Tiptap, Vite 5 |
| Database   | PostgreSQL 15 (Docker), MySQL config available in `.env.example` |
| Mail       | Gmail API (OAuth), `log` mailer by default                  |
| Dev env    | Docker Compose (Apache, PHP, Node 22, Composer, Xdebug)     |
| Quality    | PHPUnit, Larastan, Pint                                     |

## Project Structure

```
app/
  Http/Controllers/
    Admin/            Dashboard, Pages, Navigation, Database viewer, Gmail OAuth
    Auth/, Settings/  Login and account settings
    ContactController.php, PublicPageController.php
  Models/             Page, NavigationItem, User, GoogleToken
  Services/           GmailMailer
  Logging/            SplitByLevel log channel
database/
  migrations/         pages, navigation_items, google_tokens, users (is_admin, 2FA), etc.
  seeders/            SuperAdminSeeder, HomePageSeeder
docker/               Dockerfile, Apache vhost, PHP/Xdebug configuration
resources/js/
  Components/         NavBar, WysiwygEditor
  Layouts/            PublicLayout, AdminLayout
  app.js, ssr.js      Client entry and SSR entry
  Pages/              Public/*, Admin/*, Auth/Login
routes/               web.php, auth.php, settings.php, console.php
tests/                Feature and Unit tests
```

## Getting Started (Docker)

### Prerequisites

- Docker and Docker Compose
- Git

On Linux/WSL2, set your host user IDs so files created in the container are owned by you:

```bash
export HOST_UID=$(id -u)
export HOST_GID=$(id -g)
```

You can also put `HOST_UID` and `HOST_GID` in `.env`.

### Setup

1. **Clone and configure**

   ```bash
   git clone <your-repo-url> laravel-portfolio
   cd laravel-portfolio
   cp .env.example .env
   ```

2. **Set your admin credentials** in `.env`:

   ```env
   SUPER_ADMIN_NAME="Your Name"
   SUPER_ADMIN_EMAIL=you@example.com
   SUPER_ADMIN_PASSWORD=choose-a-strong-password
   ```

3. **Start the containers**

   ```bash
   docker compose up -d --build
   ```

4. **Install dependencies, generate the app key, migrate, and seed**

   ```bash
   docker compose run --rm cli composer install
   docker compose run --rm cli php artisan key:generate
   docker compose run --rm cli php artisan migrate --seed
   docker compose run --rm cli npm install
   docker compose run --rm cli npm run build
   ```

5. **Open the site** at <http://localhost:8080> (change the port with `APP_PORT`).
   The admin area is at `/admin/dashboard`; sign in at `/login`.

> The Compose file overrides the database settings for the containers (`DB_CONNECTION=pgsql`, host `pgsql`), so the MySQL defaults in `.env.example` are ignored when running through Docker.

### Services

| Service | Purpose                                   | Default port |
| ------- | ----------------------------------------- | ------------ |
| `app`   | Apache + PHP web server                   | `8080`       |
| `cli`   | One-off Composer / Artisan / npm commands | none         |
| `pgsql` | PostgreSQL 15 with a persistent volume    | `5432`       |

Database defaults are `portfolio` / `portfolio` / `portfolio` (database, user, password) and can be overridden via `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

## Development

Run everything through the `cli` container:

```bash
# Artisan
docker compose run --rm cli php artisan <command>

# Rebuild frontend assets
docker compose run --rm cli npm run build

# Vite dev server (HMR)
docker compose run --rm cli npm run dev
```

The `cli` container does not publish the Vite port. To use HMR, add a `5173:5173` port mapping to the `cli` service and run Vite with `--host`.

### Queue and scheduler

`QUEUE_CONNECTION` defaults to `database`, and `routes/console.php` schedules `queue:work --stop-when-empty` every minute. Make sure the scheduler is running (`php artisan schedule:work` locally, or a cron entry for `schedule:run` in production) so queued jobs are processed.

### Testing and code quality

```bash
docker compose run --rm cli composer test         # Pint check, PHPStan, then the PHPUnit suite
docker compose run --rm cli composer lint         # Auto-fix code style with Pint
docker compose run --rm cli composer lint:check   # Style check only
docker compose run --rm cli composer types:check  # Static analysis (Larastan)
docker compose run --rm cli php artisan test      # PHPUnit only
```

Xdebug is preinstalled; adjust `docker/php/conf.d/xdebug.ini` to match your IDE.

## Server-Side Rendering (production)

Public pages are rendered to HTML on the server by a small Node process, so crawlers and link scrapers see real content instead of an empty shell. Admin routes (`admin/*`, `login`) are excluded via `$withoutSsr` in `HandleInertiaRequests`.

- `npm run build` builds **both** bundles: the browser bundle (`public/build`) and the SSR bundle (`bootstrap/ssr/ssr.js`). All dependencies are bundled into that one file, so the SSR process needs only Node 22+ and the file itself.
- The SSR server is **not needed locally**. Keep `INERTIA_SSR_ENABLED=false` in your dev `.env`.
- If the SSR server is down or errors, Inertia falls back to normal browser rendering, so the site stays up.

### Running it

Start it once as a long-running process and let a process manager restart it if it exits:

```bash
php artisan inertia:start-ssr
```

Set these in the production `.env`:

```env
INERTIA_SSR_ENABLED=true
INERTIA_SSR_URL=http://127.0.0.1:13714
```

The server binds to `127.0.0.1:13714`. If you run it in a **separate container** from the app, change the host in `resources/js/ssr.js` (to `0.0.0.0`) and point `INERTIA_SSR_URL` at that container (for example `http://ssr:13714`).

Example Supervisor program (adjust paths):

```ini
[program:inertia-ssr]
command=php /var/www/html/artisan inertia:start-ssr
directory=/var/www/html
autostart=true
autorestart=true
stopasgroup=true
```

### Every deploy

You only need these steps when frontend code changed (content edits made in the admin need nothing):

```bash
npm ci
npm run build
php artisan inertia:stop-ssr --graceful   # process manager starts a fresh one with the new bundle
```

Without Octane, PHP code changes are picked up on the next request. If you run PHP-FPM with `opcache.validate_timestamps=0`, reload PHP-FPM (or restart the container) after deploying.

Check that it is healthy with `php artisan inertia:check-ssr`, or confirm the HTML contains your content without JavaScript: `curl -s https://your-domain | grep "<h1"`.

## SEO

Each CMS page has SEO fields in the admin (**Admin > Pages > Edit**), with character counters and a search-result preview:

| Field | Used for | Fallback when blank |
| ----- | -------- | ------------------- |
| SEO title | `<title>` and `og:title`, used exactly as entered | `{page title} - Benjamin Mastrangelo` (home page: just the site name) |
| Meta description | `meta description` and `og:description` | Site-wide default in `resources/js/seo.js` |
| Keywords | `meta keywords` (omitted when blank) | none |

Every public page also gets a canonical link and `og:url` for its own URL, plus `og:type`.

The tags are rendered by `resources/js/Components/SeoHead.vue`, which is used by the public pages (`Home`, `Page`, `Contact`) and is server-rendered when SSR is on. To change the site-wide defaults, edit `resources/js/seo.js` and rebuild. The `Contact` page description is set in `Contact.vue`.

Recommended lengths are about 60 characters for the title and 160 for the description. Note that Google ignores the keywords meta tag, so the title and description do the real work.

After deploying, run `php artisan migrate` to add the new columns (`meta_title`, `meta_keywords`) to `pages`.

## Configuration

### Gmail contact form

The contact form sends mail through the Gmail API. `config/gmail.php` reads these variables, which are **not** in `.env.example`, so add them yourself:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8080/admin/gmail/callback
CONTACT_TO_EMAIL=you@example.com
```

1. Create OAuth credentials in the [Google Cloud Console](https://console.cloud.google.com/) with the Gmail API enabled.
2. Add the redirect URI above as an authorized redirect URI.
3. Sign in as admin and visit `/admin/gmail/connect` to authorize. The token is stored in the `google_tokens` table.

If sending fails, the visitor sees a friendly error and the exception is reported through the normal logging pipeline.

### Other environment variables

| Variable                    | Description                                          |
| --------------------------- | ---------------------------------------------------- |
| `SUPER_ADMIN_NAME/EMAIL/PASSWORD` | Credentials used by `SuperAdminSeeder`         |
| `MAIL_EXCEPTION_RECIPIENT`  | Address that receives exception notification emails  |
| `INERTIA_SSR_ENABLED` / `INERTIA_SSR_URL` | Turn SSR on and where the SSR server listens (see above) |
| `HOST_UID` / `HOST_GID`     | Match container file ownership to your host user     |
| `APP_PORT`                  | Host port for the web container (default `8080`)     |

### Super-admin seeder

`SuperAdminSeeder` is safe to run repeatedly. It creates the admin if the email doesn't exist and otherwise syncs the name and password, never creating duplicates. It skips with a warning if `SUPER_ADMIN_EMAIL` or `SUPER_ADMIN_PASSWORD` is missing.

```bash
docker compose run --rm cli php artisan db:seed --class=SuperAdminSeeder
```

## Routes Overview

| Route                     | Description                                    |
| ------------------------- | ---------------------------------------------- |
| `/`                       | Home page                                      |
| `/contact`                | Contact form (POST is throttled)               |
| `/{slug}`                 | Dynamic CMS pages (catch-all, always last)     |
| `/login`                  | Sign in                                        |
| `/settings/*`             | Profile, security, and appearance settings     |
| `/admin/dashboard`        | Admin dashboard                                |
| `/admin/pages`            | Page management (CRUD)                         |
| `/admin/navigation`       | Navigation builder                             |
| `/admin/database`         | Read-only database browser                     |
| `/admin/gmail/connect`    | Start Gmail OAuth flow                         |

## Logging

The `SplitByLevel` channel (`app/Logging/SplitByLevel.php`) writes each log level to its own daily-rotating file under `storage/logs/` (for example `error.log`, `warning.log`), keeping 14 days by default.

## License

Copyright (c) 2026 wilecoyte78. All rights reserved. See [LICENSE](LICENSE) for details.
