<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🚀 No-code Render Deploy

Deploy the Slides app to [Render](https://render.com) in a few clicks — no terminal required.

### Step 1 — Fork or use this repo

Make sure the repository is on your GitHub account (or an organization you own).

### Step 2 — Create a Blueprint on Render

1. Go to [render.com](https://render.com) and sign in.
2. Click **New +** → **Blueprint**.
3. Connect your GitHub account if prompted, then select the **slides** repository.
4. Click **Apply** / **Connect**.

Render will automatically create:
- **Web service** (Laravel app)
- **Worker service** (queue processor)
- **PostgreSQL** database
- **Redis** instance

### Step 3 — Set required environment variables

After the Blueprint is created, go to the **Web service** → **Environment** tab and add:

| Variable | Value |
|---|---|
| `OPENAI_API_KEY` | Your OpenAI API key (get one at [platform.openai.com](https://platform.openai.com/api-keys)) |

Everything else (database, Redis, app key) is configured automatically by the Blueprint.

> **Note:** Do the same for the **Worker service** — set `OPENAI_API_KEY` there too.

### Step 4 — Deploy

Click **Save Changes** / **Deploy** and wait for the status to show **Live** (usually 3–5 minutes).

### Step 5 — Verify the app is running

1. Click **Open in browser** on the Web service.
2. You should see the presentations list page.
3. Confirm the health endpoint works: open `https://your-app.onrender.com/health` — it should return `{"status":"ok"}`.

### Troubleshooting

| Problem | What to do |
|---|---|
| **Build failed** | Check the **Logs** tab in the web service. Most common cause: a missing system package. Open an issue with the log output. |
| **App shows error on boot** | Usually `APP_KEY` missing — Render generates it automatically via the Blueprint. If missing, go to **Environment** → add `APP_KEY` and run `php artisan key:generate --show` locally to get a value. |
| **"OPENAI_API_KEY not set"** | Go to Web service → **Environment** → add `OPENAI_API_KEY`. Redeploy. |
| **Database migration errors** | Check Logs. Migrations run automatically on startup. If they fail, you can trigger a manual deploy to retry. |
| **Queue jobs not processing** | Make sure the **Worker service** is also running (green status) and has the same env vars as the web service. |

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
