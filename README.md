<div align="center">
  <img src="./public/favicon.svg" alt="CivicGuard Logo" width="100"/>
  <h1>CivicGuard</h1>
  <p>Incident reporting, monitoring, and curfew tracking for Barangay Maimpis</p>
  <p><strong>Live site:</strong> <a href="https://civicguard.wasmer.app">https://civicguard.wasmer.app</a></p>
</div>

---

CivicGuard is a web-based system that lets residents of Barangay Maimpis, City of San Fernando, Pampanga report incidents and follow their progress. Barangay staff get one place to review, assign, and resolve reports and to log curfew violations. An AI chatbot helps residents describe what happened, and urgent messages get immediate emergency guidance.

---

## Features

- **Incident reporting:** pick a category, describe the problem, add a location, and optionally a photo and GPS pin
- **AI chatbot:** a chat that helps residents write a report, with emergency warnings in English and Tagalog
- **AI suggestions:** automatic category and urgency rating, which staff can always change
- **Report tracking:** follow a report from Received to Assigned, In progress, and Resolved, with notifications
- **Assigned tanod details:** residents can see the tanod assigned to each report in My Reports
- **Confirm or reopen:** residents can confirm a fix or reopen a report within 7 days
- **Incident queue:** reports sorted by urgency, with tabs, search, and filters
- **Role-based access:** residents, tanods, officials, and admins, where tanods only see reports assigned to them
- **Profiles:** residents can update their name, email, and profile picture, with a crop/zoom preview before saving
- **Residence verification:** residents can submit proof of Maimpis residency; admins and barangay officials review it, and approved residents receive a persistent verified badge
- **Curfew logging:** tanods can record curfew violations
- **Analytics:** charts, key numbers, and AI-written insights
- **Audit logs and PDF exports:** a record of key report, staff, and verification actions, with downloadable reports
- **Notifications:** in-app updates for report assignments, report changes, and account/verification events
- **Admin test-data reset:** clear operational test records and related report/verification uploads while preserving user accounts, profile pictures, and report categories
- **Install as an app:** works on iPhone and Android from the browser, with an offline page
- **English and Tagalog**, with light and dark mode
- **Privacy tools:** account deletion, a privacy policy, and terms of use

---

## How it works

1. **Create an account.** Sign up as a resident.
2. **Tell us what happened.** Pick a category, describe the problem, and add where it happened.
3. **Follow it.** Barangay staff review your report and post updates you can follow.

---

## Who uses it

| Who | What they do |
| --- | --- |
| **Residents** | File reports, follow progress and assigned tanods, manage their profile, and request residence verification |
| **Tanods** | Handle reports assigned to them and record curfew violations |
| **Officials** | Review and assign reports, and review residence verification requests |
| **Admin** | Manage staff accounts, reports, audit logs, residence verification, and the test-data reset |

New sign-ups are always residents.

---

## Built with

**Backend**

- Laravel 12
- PHP 8.2+
- MySQL 8.4

**Frontend**

- Blade
- Alpine.js 3
- Tailwind CSS 4
- Vite 6
- Chart.js

**Services & tools**

- Google Gemini and Groq (automatic failover)
- DomPDF
- Laravel Sail (Docker)

---

## Getting started

You need Docker (via WSL2 on Windows), Composer, and Node.js. The app uses MySQL through Laravel Sail. Set a unique `DB_PASSWORD` in `.env` before starting the containers, then use the commands below.

```bash
git clone https://github.com/miconvr/civicguard.git
cd civicguard
cp .env.example .env        # then add your Gemini and Groq keys
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
npm install && npm run dev
```

Run the tests with `./vendor/bin/sail artisan test`.

### Database migrations

The `database/migrations` directory is the source of truth for the schema. It includes the `resident_verifications` table and the report, audit log, notification, and staff-assignment fields used by the app. For a new clone, `migrate --seed` creates the schema and default report categories. Do not create these tables manually with SQL.

When you pull an update that adds a migration, apply pending migrations to your local database:

```bash
./vendor/bin/sail artisan migrate
```

Check local migration state with `./vendor/bin/sail artisan migrate:status`. Avoid `migrate:fresh` if you need to keep your local database data, because it drops and recreates application tables.

### Uploaded image storage

Report photos, profile pictures, and pending residence proof images use the private `report_photos` disk. It defaults to `storage/app/public`; for local development, ensure the app can write to that directory. You can set `PHOTO_DISK_ROOT` in `.env` to use a different local directory. The database stores image paths, while image files live on this disk. Verification proof images are deleted after an admin or official records a decision.

---

## Good to know

- **Not for emergencies.** Reports are reviewed by staff and are not monitored in real time. In an emergency, call 911 or go to the barangay hall.
- **AI is only a helper.** It can suggest a category or an urgency level, but barangay staff always make the final decision.
- **Your privacy.** Read how your data is handled in the [Privacy Policy](https://civicguard.wasmer.app/privacy).

---

## Developed by

Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez
