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
* **Incident queue** for staff: urgency sorting, status tabs, search, and a detail drawer
* **Progress timeline** (Received, Assigned, In progress, Resolved) for residents and staff
* **"Was this fixed?"** follow-up: residents confirm or reopen a resolved report within 7 days
* **Notifications** for status changes, assignments, and reopened reports
* **Analytics** with charts, KPI cards, and on-demand AI insights
* **Curfew logging** for tanods
* **Audit logs** and PDF exports
* **English and Tagalog**, plus light and dark mode

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

Laravel 11 (Breeze, Blade, Alpine.js), Tailwind CSS, MySQL, Laravel Sail (Docker), DomPDF, Chart.js, and the Google Gemini API.

---

## Requirements

* **Docker Desktop** installed and running
* **Git**
* A terminal (Windows: WSL/Ubuntu recommended; macOS/Linux: native terminal)
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

Open `.env` and set your key (never commit this file):

```
GEMINI_API_KEY=your-key-here
```

### 3. Install PHP dependencies

A fresh clone has no `vendor/` folder yet, so use this one-time command. If your `docker-compose.yml` uses a different PHP version, match the image tag.

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
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

> **Using an existing database?** Import your CivicGuard `.sql` backup through phpMyAdmin instead of running `migrate --seed` on it.

### 7. Install and start the frontend

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Keep `npm run dev` running while developing. For a one-time build, use `./vendor/bin/sail npm run build`.

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
./vendor/bin/sail npm run dev    # frontend
./vendor/bin/sail stop           # stop
```

## Local Services

| Service | URL |
| --- | --- |
| **CivicGuard** | http://localhost |
| **phpMyAdmin** | http://localhost:8080 |

Use the database credentials from your `.env` file for phpMyAdmin.

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
