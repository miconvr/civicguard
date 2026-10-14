<p align="center">
  <img src="./public/favicon.svg" alt="CivicGuard Logo" width="100"/>
</p>

<h1 align="center">CivicGuard</h1>

<p align="center">
  Incident reporting, monitoring, and curfew tracking for Barangay Maimpis
</p>

---

## Features

* **Incident reporting** with photo upload, optional GPS pin, and an AI category suggestion
* **AI Assistant** that helps residents describe an incident in their own words
* **Emergency guidance** in the chat: urgent messages (for example a weapon or fire) get an instant fixed warning with a Call 911 button and an "I'm safe, continue my report" option
* **Incident queue** for staff: urgency sorting, status tabs, search, and a detail drawer
* **Progress timeline** (Received, Assigned, In progress, Resolved) for residents and staff
* **"Was this fixed?"** follow-up: residents confirm or reopen a resolved report within 7 days
* **Notifications** for status changes, assignments, and reopened reports
* **Analytics** with charts, KPI cards, and on-demand AI insights
* **Curfew logging** for tanods
* **Audit logs** and PDF exports
* **English and Tagalog**, light and dark mode in the signed-in app, and a light-only public experience
* **Responsive navigation** with a collapsible desktop sidebar and mobile drawer

## User Roles

| Role | What they can do |
| --- | --- |
| **Resident** | File reports, track them, confirm or reopen resolved ones, use the AI Assistant |
| **Tanod** | See the queue, update reports assigned to them, log curfew violations |
| **Official** | Everything a tanod can see, plus assign reports and correct severity |
| **Admin** | Everything above, plus add staff accounts |

New sign-ups are always created as residents.

---

## Tech Stack

Laravel 12 (Breeze, Blade, Alpine.js), PHP 8.2 or later, Tailwind CSS 4, MySQL 8.4, Laravel Sail (Docker), DomPDF, Chart.js, and the Google Gemini API.

---

## Requirements

* **Docker Desktop** with Docker Compose, installed and running
* **Git**
* A terminal (Windows: use WSL/Ubuntu for the commands below; macOS/Linux: use a native terminal)
* A **Gemini API key** (optional, see [AI features](#ai-features-and-privacy))

---

## Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/miconvr/civicguard.git
cd civicguard
```

### 2. Create the environment file

```bash
cp .env.example .env
```

Open `.env` and set your key if you want the Gemini-powered features (never commit this file):

```
GEMINI_API_KEY=your-key-here
```

Set `DB_PASSWORD` to a unique, non-empty password before starting Docker Compose. The same value is used for the local MySQL account and phpMyAdmin. `.env` is local configuration and must not be committed.

The database is published on `127.0.0.1:3340` by default; change `FORWARD_DB_PORT` if that port is already in use. The app uses port `80` by default; set `APP_PORT` in `.env` if it is occupied.

### 3. Install PHP dependencies

A fresh clone has no `vendor/` folder yet. Use the published Sail PHP 8.4 Composer helper for the initial dependency install. The app runtime itself is built from PHP 8.5; `composer.json` requires PHP `^8.2` and resolves dependencies against the PHP 8.2.12 platform:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install
```

### 4. Start Docker

```bash
./vendor/bin/sail up -d
```

### 5. Generate the application key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Set up the database and photo storage

```bash
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

`migrate --seed` is for a fresh local database. If you already have CivicGuard data, back it up first and run `./vendor/bin/sail artisan migrate` to apply pending migrations. Do not use `migrate:fresh` or re-seed an existing database. If you are moving an existing database to this computer, restore its SQL backup before starting the app.

### 7. Install and start the frontend

```bash
./vendor/bin/sail npm ci
./vendor/bin/sail npm run dev
```

Keep the Vite development command running while developing. For a one-time production asset build, use `./vendor/bin/sail npm run build`.

### 8. Create the first admin

1. Open http://localhost/register and create an account.
2. Promote it to admin (replace the email):

```bash
./vendor/bin/sail artisan tinker --execute="App\Models\User::where('email', 'you@example.com')->update(['role' => 'admin']); echo 'done' . PHP_EOL;"
```

3. Log in again. Admins can create tanod and official accounts from **Add Staff**.

---

## Daily Development

```bash
cd ~/civicguard
./vendor/bin/sail up -d          # start
./vendor/bin/sail npm run dev    # keep running in this terminal while developing
```

Stop the development server with `Ctrl+C`, then stop the containers:

```bash
./vendor/bin/sail stop
```

## Local Services

| Service | URL |
| --- | --- |
| **CivicGuard** | http://localhost (or `http://localhost:<APP_PORT>` if you changed the port) |
| **phpMyAdmin** | http://localhost:8080 |

Use the database credentials from your `.env` file for phpMyAdmin.

### Port already in use

If Docker cannot publish a port, change the matching value in `.env`, then recreate the containers:

```dotenv
APP_PORT=8081
FORWARD_DB_PORT=3341
```

```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

Use `APP_PORT` for a conflict on the web app port and `FORWARD_DB_PORT` for a conflict on the host MySQL port. This changes the host port only; the app container continues to reach MySQL at `mysql:3306`. `sail down` preserves the named MySQL volume. Do not add `-v` if you need to keep the local database.

---

## Production deployment

CivicGuard is deployed on Wasmer Edge at <https://civicguard.wasmer.app>, configured by `app.yaml`. Secrets (`APP_KEY`, database credentials, `GEMINI_API_KEY`) are set in the Wasmer dashboard and never committed.

To release a change: test locally, `git push`, then run `wasmer deploy`. Pushing alone does not update the live site. Run any new migration against the live database once per release.

Before serving real residents, get the barangay's approval for sending report text to Google Gemini, publish a privacy notice, and configure real email delivery.

## Running tests

With the Sail container running, execute the Laravel feature and unit tests with:

```bash
./vendor/bin/sail artisan test
```

Check the production frontend bundle with:

```bash
./vendor/bin/sail npm run build
```

## UI motion

Pages use a brief entrance, selected landing and dashboard content reveals as it scrolls into view, and restrained hover feedback on interactive cards, buttons, and navigation. The signed-in account menu uses an origin-aware dropdown transition, and successful report actions show a brief animated check. Motion respects `prefers-reduced-motion` and does not hide content when animation is disabled. The CSS is included in the app, with no runtime package dependency. The dropdown and success-check patterns are adapted from [transitions.dev](https://transitions.dev/), whose free recipes may be used in personal and commercial projects under its terms. See the [Transitions.dev repository](https://github.com/Jakubantalik/transitions.dev).

---

## AI Features and Privacy

The AI Assistant, category suggestion, severity classification, and analytics insights use the Google Gemini API through `GEMINI_API_KEY`.

* **Without a key**, the app still works: severity uses keyword matching, the assistant answers from built-in FAQs, insights use rule-based summaries, and category suggestion stays empty.
* **With a key**, report text (descriptions and categories) is sent to Google for processing. Report content can include personal details, so check that this is acceptable for your barangay and mention it in your privacy notice.

---

## Developed by

Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez

## Repository

https://github.com/miconvr/civicguard.git
