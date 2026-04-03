<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🚀 Low-cost Deploy: Vercel + Render + Supabase

This stack is optimised for small traffic (≤ 2 concurrent users).  
**Redis and Render-managed Postgres are removed** — queues use the database driver, sessions and cache use the filesystem.

| Layer | Service | Notes |
|---|---|---|
| Frontend (Blade/Livewire) | [Render](https://render.com) web service | Served by the same Laravel container |
| Backend API + queue worker | [Render](https://render.com) | Deployed from `render.yaml` Blueprint |
| Database | [Supabase](https://supabase.com) Postgres | Free tier available |
| Redis | **none** | Removed — not needed at this scale |

---

### Step 1 — Create a Supabase database

1. Go to [supabase.com](https://supabase.com), create a free account and a new **Project**.
2. Once the project is ready, open **Project Settings → Database**.
3. Copy the connection details you will need:
   - **Host** (looks like `db.<project-ref>.supabase.co`)
   - **Database name** (usually `postgres`)
   - **User** (usually `postgres`)
   - **Password** (the one you chose when creating the project)
   - **Port** (`5432`)

---

### Step 2 — Deploy backend to Render

1. Go to [render.com](https://render.com), sign in, click **New +** → **Blueprint**.
2. Connect your GitHub account and select this repository.
3. Click **Apply** / **Connect**.

Render will create:
- **slides-web** — Laravel web service
- **slides-worker** — queue worker

> No Postgres or Redis is provisioned by Render — the database lives in Supabase (see Step 3).

---

### Step 3 — Set required environment variables in Render

After the Blueprint is created, go to **slides-web → Environment** and fill in:

| Variable | Where to get it |
|---|---|
| `DB_HOST` | Supabase → Project Settings → Database → Host |
| `DB_DATABASE` | Supabase → usually `postgres` |
| `DB_USERNAME` | Supabase → usually `postgres` |
| `DB_PASSWORD` | Supabase → your project password |
| `OPENAI_API_KEY` | [platform.openai.com/api-keys](https://platform.openai.com/api-keys) |
| `FRONTEND_URL` | Your Vercel app URL, e.g. `https://your-app.vercel.app` (optional, enables CORS) |

`DB_PORT`, `DB_SSLMODE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, `CACHE_STORE`, and `APP_KEY` are pre-configured automatically by the Blueprint — no action needed.

The **slides-worker** service inherits `DB_*` and `OPENAI_API_KEY` from `slides-web` automatically.

---

### Step 4 — Run database migrations

After the first deploy completes, open **slides-web → Shell** (or trigger a one-off job) and run:

```bash
php artisan migrate --force
```

> Migrations also run automatically on every deploy via the Docker start script.

---

### Step 5 — (Optional) Deploy frontend to Vercel

If you build a separate frontend (e.g. Next.js), deploy it to [vercel.com](https://vercel.com):

1. Import this repository (or a separate frontend repo) into Vercel.
2. Set `NEXT_PUBLIC_API_URL` (or equivalent) to your Render web service URL.
3. Copy the Vercel deployment URL and paste it as `FRONTEND_URL` in Render (enables CORS).

For the default Blade/Livewire UI, the frontend is already served by the Laravel container on Render — no separate Vercel deployment is needed.

---

### Step 6 — Verify the app is running

1. Click **Open in browser** on the **slides-web** service in Render.
2. You should see the presentations list page.
3. Confirm the health endpoint: `https://your-app.onrender.com/health` → `{"status":"ok"}`.

---

### Troubleshooting

| Problem | What to do |
|---|---|
| **Build failed** | Check the **Logs** tab in the web service. Most common cause: a missing system package. Open an issue with the log output. |
| **App shows error on boot** | Usually `APP_KEY` missing — Render generates it automatically via the Blueprint. If missing, go to **Environment** → add `APP_KEY` and run `php artisan key:generate --show` locally to get a value. |
| **Database connection error** | Double-check the `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` values in Render match your Supabase project. Ensure `DB_SSLMODE=require`. |
| **"OPENAI_API_KEY not set"** | Go to **slides-web → Environment** → add `OPENAI_API_KEY`. Redeploy. |
| **Migration errors on first deploy** | Run `php artisan migrate --force` manually via Render Shell, or trigger a new deploy. |
| **Queue jobs not processing** | Make sure **slides-worker** is running (green status) in Render. |
| **CORS errors from Vercel** | Set `FRONTEND_URL` to your Vercel domain in the **slides-web** environment. |

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
