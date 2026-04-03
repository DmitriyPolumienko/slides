# Setup Guide

## Requirements
- PHP 8.3+
- Composer 2+
- Node.js 18+
- SQLite (built into PHP)

## Installation

```bash
# Clone the repository
git clone <repo-url>
cd slides

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations and seed data
php artisan migrate --seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | `sqlite` |
| `DB_DATABASE` | Path to SQLite file | `database/database.sqlite` |
| `OPENAI_API_KEY` | OpenAI API key (optional) | `` |
| `OPENAI_MODEL` | OpenAI model to use | `gpt-4o` |

## Without OpenAI

The app works without an OpenAI key. When no key is configured, the AI generation service returns mock content for testing.
