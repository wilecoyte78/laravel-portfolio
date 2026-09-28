# Portfolio Site — Laravel + Octane/Swoole + Inertia/Vue + Tailwind (Docker)

## Stack
- Laravel (scaffolded via the official `laravel new --vue` starter kit: Inertia + Vue 3 + Tailwind + auth, out of the box)
- Laravel Octane running on **Swoole**
- MySQL, all run via Docker Compose
- TipTap WYSIWYG editor for page content
- A `Page` + `NavigationItem` system: pages are created by the admin and manually attached to a nav
  slot (they are **not** auto-injected — see "How navigation works" below, since you specifically
  wanted control over nesting, e.g. About → Bio / About → Resume with "About" itself unlinked).

## How the build works
`app-src/` contains only the *custom* application code (models, controllers, migrations,
seeders, routes, Vue pages/components, and Tailwind/Vite config). The `Dockerfile`:

1. Installs the official Laravel installer and runs `laravel new . --vue --npm --database=mysql`
   to generate a fresh, current Laravel + Inertia + Vue + Tailwind skeleton.
2. Adds `laravel/octane` + `tightenco/ziggy`, and runs `php artisan octane:install --server=swoole`.
3. Copies everything in `app-src/` on top of that skeleton (this is what actually builds *your*
   site — pages, navigation, admin panel).
4. Builds the Vite/Tailwind frontend assets in a Node stage.
5. Ships a final image that boots via `docker/entrypoint.sh`, which generates the app key,
   runs migrations, seeds/updates the super admin from your `.env`, and starts
   `php artisan octane:start --server=swoole`.

## Setup

```bash
cp .env.example .env
# then edit .env and set real values, especially:
#   DB_PASSWORD, DB_ROOT_PASSWORD
#   SUPER_ADMIN_NAME, SUPER_ADMIN_EMAIL, SUPER_ADMIN_PASSWORD

docker compose build
docker compose up -d
```

The site will be at `http://localhost:8000`, admin login at `http://localhost:8000/login`.

Log in with the `SUPER_ADMIN_EMAIL` / `SUPER_ADMIN_PASSWORD` from your `.env`. That seeder
(`database/seeders/SuperAdminSeeder.php`) runs on every container start — if you change the
password in `.env` and restart the stack, the admin's password updates too. No duplicate
accounts are ever created.

## How navigation works

- **Admin → Pages**: create a page (title, slug, WYSIWYG content, publish toggle).
- **Admin → Navigation**: this is where a page actually becomes visible on the site.
  - "Add top-level item" with a page selected → simple nav link.
  - "Add top-level item" with **no page** selected → a dropdown header only (e.g. "About"),
    which will render as a hover/click dropdown but has no link of its own.
  - Click "+ sub-item" on that header to add children under it (e.g. "Bio", "Resume"), each
    pointed at its own page.
  - Use the ↑ / ↓ buttons to reorder items within their level.
- This is intentionally a manual step rather than auto-adding every new page to the nav, so you
  can draft pages before deciding where (or whether) they appear in the menu.

## Project layout

```
docker-compose.yml
Dockerfile
docker/entrypoint.sh
.env.example
app-src/
  app/Models/{Page,NavigationItem,User}.php
  app/Http/Controllers/{PublicPageController, Admin/*, Auth/*}.php
  app/Http/Middleware/{EnsureUserIsAdmin, HandleInertiaRequests}.php
  bootstrap/app.php               # registers the 'admin' middleware alias
  routes/{web,auth}.php
  database/migrations/*
  database/seeders/{DatabaseSeeder,SuperAdminSeeder}.php
  resources/js/
    app.js
    Layouts/{PublicLayout,AdminLayout}.vue
    Components/{NavBar,WysiwygEditor}.vue
    Pages/Public/{Home,Page}.vue
    Pages/Auth/Login.vue
    Pages/Admin/Dashboard.vue
    Pages/Admin/Pages/{Index,Create,Edit}.vue
    Pages/Admin/Navigation/Index.vue
  resources/css/app.css
  package.json, vite.config.js, tailwind.config.js, postcss.config.js
```

## Contact form (Gmail API)

The site includes a `/contact` page that emails you via the Gmail API — not SMTP — using OAuth.
One-time setup:

1. **Google Cloud Console** (console.cloud.google.com): create a project (or use an existing
   one), then **APIs & Services → Library** → enable the **Gmail API**.
2. **APIs & Services → OAuth consent screen**: set up an External consent screen. Add your own
   Gmail address as a test user. **Publishing status matters**: if left in "Testing", refresh
   tokens expire after 7 days and you'll have to reconnect weekly. Set it to **"In production"**
   for a non-expiring token — you'll see an "unverified app" warning when you connect, which is
   expected and fine to click through for a single-owner app.
3. **APIs & Services → Credentials → Create Credentials → OAuth client ID**, type **Web
   application**. Under "Authorized redirect URIs" add exactly:
   `https://benjaminmastrangelo.com/admin/gmail/callback`
4. Copy the generated Client ID and Client Secret into `.env`:
   ```
   GOOGLE_CLIENT_ID=...
   GOOGLE_CLIENT_SECRET=...
   GOOGLE_REDIRECT_URI=https://benjaminmastrangelo.com/admin/gmail/callback
   CONTACT_TO_EMAIL=your@email.com
   ```
5. Rebuild/restart the stack so the new env vars and migration take effect, then in
   **Admin → Dashboard**, click **Connect Gmail** and approve access (using the same Google
   account you added as a test user / that you want sending the emails).

Once connected, the refresh token is stored in the database (`google_tokens` table) — it
survives container restarts. Contact form submissions arrive as emails sent from that Gmail
account, with the visitor's name/email set as the Reply-To so you can just hit reply.

Add a "Contact" link to your navigation by adding a nav item with External URL set to `/contact`
(it's a functional route, not a database-backed Page, so it won't appear in the Page dropdown).
- The starter kit also scaffolds its own registration/password-reset flow; this project's
  `routes/web.php` + `routes/auth.php` intentionally only expose login/logout, since a
  single-admin portfolio site shouldn't have public self-registration. You can wire the
  starter kit's password-reset controller back in later if you want a "forgot password" flow.
- `Page::content` is rendered with `v-html` on the public site — safe here since only the
  authenticated super admin can write it, but don't allow untrusted users to submit page content
  without adding HTML sanitization first.
- No `nginx` reverse proxy is included; Octane/Swoole serves HTTP directly on port 8000. Put this
  behind a reverse proxy (Nginx/Caddy) with TLS for production use.

