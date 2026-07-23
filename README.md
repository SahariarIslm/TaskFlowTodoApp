# TaskFlow

TaskFlow is a Laravel and Vue task management application. It includes email/password authentication, OTP verification, optional Google login, a protected dashboard, task CRUD, task comments, profile/settings pages, project counts, search, filters, tags, and assignee fields.

The Laravel application lives in the `taskflow/` directory. Run all setup and development commands from that folder unless a command says otherwise.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum
- Laravel Socialite
- Vue 3
- Vue Router
- Pinia
- Axios
- Tailwind CSS 4
- Vite
- SQLite, MySQL, or MariaDB

## Prerequisites

Install these before cloning the project:

- Git
- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite, MySQL, or MariaDB

On Windows with WAMP/XAMPP, make sure `php`, `composer`, `node`, and `npm` are available from PowerShell or your terminal.

## Fresh Installation

Clone the repository:

```bash
git clone https://github.com/SahariarIslm/TaskFlowTodoApp.git
cd TaskFlow/taskflow
```

Install backend dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the local environment file:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

Set the app URL in `.env`:

```env
APP_URL=http://localhost:8000
```

## Database Setup

The default `.env.example` uses SQLite:


For MySQL or MariaDB, create a database manually first, then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate
```

## Mail and OTP Setup

TaskFlow sends an OTP when a user registers or tries to log in before verification.

For local development, `.env.example` logs emails instead of sending real mail:

```env
MAIL_MAILER=log
```

After registering, find the OTP in:

```text
taskflow/storage/logs/laravel.log
```

To send real emails, replace the `MAIL_*` values in `.env` with your SMTP provider settings.

## Google Login Setup Optional

Google login is supported, but each developer must provide their own Google OAuth credentials.

In Google Cloud Console:

1. Open `APIs & Services > Credentials`.
2. Create an `OAuth 2.0 Client ID`.
3. Add this authorized redirect URI:

```text
http://localhost:8000/api/auth/google/callback
```

Add the credentials to `.env`:

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/api/auth/google/callback"
```

If your Laravel server uses a different URL, update both `APP_URL` and the Google Cloud authorized redirect URI so they match exactly.

Clear cached config after changing environment values:

```bash
php artisan config:clear
```

## Run Locally

Start the Laravel backend:

```bash
php artisan serve
```

In a second terminal, start Vite:

```bash
npm run dev
```

Windows PowerShell, if script execution blocks `npm.ps1`:

```powershell
npm.cmd run dev
```

Open the application at:

```text
http://localhost:8000
```

Keep both terminals running. The Vite URL, usually `http://localhost:5173`, only serves frontend assets for the Laravel app.

The API base URL is:

```text
http://localhost:8000/api
```

## Main Routes

Frontend pages:

- `/`
- `/login`
- `/register`
- `/verify-otp`
- `/dashboard`
- `/my-tasks`
- `/profile`
- `/settings`
- `/tasks/{id}`

API routes:

- `POST /api/auth/register`
- `POST /api/auth/verify-otp`
- `POST /api/auth/resend-otp`
- `POST /api/auth/login`
- `GET /api/auth/google/redirect`
- `GET /api/auth/google/callback`
- `GET /api/auth/me`
- `PUT /api/auth/profile`
- `POST /api/auth/logout`
- `GET /api/dashboard/stats`
- `GET /api/dashboard/projects`
- `GET /api/users`
- `GET|POST|PUT|DELETE /api/tasks`
- `POST /api/tasks/{task}/comments`

Protected API routes require a Sanctum bearer token.

## Build for Production

Build frontend assets:

```bash
npm run build
```

Run Laravel optimization commands when deploying:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

For a production server, point the web server document root to:

```text
taskflow/public
```

Make sure `storage/` and `bootstrap/cache/` are writable by the web server.

## Tests and Quality

Run the test suite:

```bash
php artisan test
```

Format PHP code:

```bash
./vendor/bin/pint
```

Windows PowerShell:

```powershell
.\vendor\bin\pint
```

## Useful Commands

Clear all Laravel caches:

```bash
php artisan optimize:clear
```

Reset the database and remove existing data:

```bash
php artisan migrate:fresh
```

Reset the database and run seeders:

```bash
php artisan migrate:fresh --seed
```

Build assets and run Laravel from one Composer script:

```bash
composer run dev
```

This starts Laravel, Vite, queue listening, and log watching together.

## Project Structure

```text
TaskFlow/
  README.md
  design files/              Static HTML/CSS design references
  taskflow/
    app/                     Laravel models, controllers, requests, resources, mail
    bootstrap/
    config/
    database/migrations/     Database schema
    public/                  Web server document root
    resources/css/           App styles
    resources/js/            Vue app, pages, layouts, components, stores, router
    routes/api.php           API routes
    routes/web.php           Vue SPA fallback route
    storage/                 Logs, cache, generated framework files
    tests/                   Feature and unit tests
```

## Troubleshooting

If `php artisan migrate` fails with SQLite, confirm `database/database.sqlite` exists.

If login or authenticated API calls fail, confirm Laravel is running and the browser has an auth token in local storage.

If OTP email does not arrive locally, check `taskflow/storage/logs/laravel.log`.

If Google login fails, confirm `APP_URL`, `GOOGLE_REDIRECT_URI`, and the Google Cloud authorized redirect URI are exactly the same.

If frontend changes do not appear, restart Vite:

```bash
npm run dev
```

If Composer or npm commands fail on Windows, confirm PHP, Composer, Node.js, and npm are added to your system `PATH`.
