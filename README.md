<p align="center">
  <img src="./public/favicon.svg" alt="CivicGuard Logo" width="100"/>
</p>

<h1 align="center">CivicGuard</h1>

<p align="center">
  Incident reporting, monitoring, and curfew tracking for Barangay Maimpis
</p>

**Live site:** <https://civicguard.wasmer.app>

CivicGuard is a web-based system that lets residents of Barangay Maimpis, City of San Fernando, Pampanga report incidents and follow their progress. Barangay staff get one place to review, assign, and resolve reports and to log curfew violations. An AI assistant helps residents describe what happened, and urgent messages get immediate emergency guidance.

---

## Features

**Reporting**

* Multipart report form with server-side validation: description up to 2,000 characters, category, location text, and an optional photo (JPG, PNG, or WebP, up to 5 MB and 8000x8000 px)
* Optional GPS pin through the browser Geolocation API, stored as latitude and longitude
* Photos are kept on a private storage disk and served through an authorized route with `nosniff` headers, never from a public URL

**AI**

* Category suggestion and severity classification (low, moderate, high, critical) through the Google Gemini API, with a keyword-based fallback so reports are never blocked when the API is down
* Conversational report assistant that collects category, description, and location and returns them as structured JSON for a review-before-submit card
* Emergency keyword detection in English and Tagalog that returns fixed guidance without calling the API
* Staff can override any AI severity rating, and every change is logged

**Workflow**

* Status pipeline: pending, in progress, resolved, with a timeline built from audit log entries
* Assignment of reports to tanods, with in-app notifications for status changes, assignments, and reopened reports
* Residents can confirm a fix or reopen a resolved report within a 7-day window

**Access control and audit**

* Role-based access (resident, tanod, official, admin) through route middleware and ownership checks
* Tanod queries are limited by an Eloquent scope to reports assigned to them and curfew logs they created, including exports and file downloads
* Audit log table that records report, assignment, severity, status, curfew, and staff actions, with search, action filters, and PDF export
* Rate limiting on login, registration, password reset, reports, and the AI endpoints, plus per-hour caps on AI calls

**Staff tools**

* Incident queue sorted by urgency, with status tabs, search, severity filter, and pagination
* Analytics dashboard with Chart.js charts, KPI cards, and on-demand AI insights cached for 30 minutes
* Curfew violation logging for tanods, wrapped in a database transaction that creates the report and the curfew record together
* PDF exports for reports, consolidated reports, analytics, and audit logs through DomPDF

**Platform**

* Installable Progressive Web App: web app manifest, service worker with an offline fallback page, `beforeinstallprompt` handling, and an animated install guide for iOS and Android
* English and Tagalog interface, light and dark mode in the signed-in app, and responsive layouts with safe-area insets
* Offline banner, request timeouts, and retry handling on forms and the chat
* Account deletion that removes a resident's reports and photo files, with a privacy policy, terms of use, and consent at registration
* Feature test suite (63 tests) covering authentication, access control, reports, the chatbot, account deletion, and the PWA

---

## How it works

1. **Create an account.** Sign up as a resident.
2. **Tell us what happened.** Pick a category, describe the problem, and add where it happened.
3. **Follow it.** Barangay staff review your report and post updates you can follow.

---

## Who uses it

| Who | What they do |
| --- | --- |
| **Residents** | File reports and follow their progress |
| **Tanods** | Handle the reports assigned to them and record curfew violations |
| **Officials** | Review and assign reports |
| **Admin** | Manages staff accounts |

New sign-ups are always residents.

---

## Built with

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Authentication | Laravel Breeze (session-based) with custom role middleware |
| Database | MySQL 8.4, Eloquent ORM, migrations with cascade and null-on-delete foreign keys |
| Frontend | Blade templates, Alpine.js 3, Tailwind CSS 4, Vite 6 |
| Charts | Chart.js |
| PDF | barryvdh/laravel-dompdf |
| AI | Google Gemini API, called server-side with the key sent in a request header |
| PWA | Web app manifest, service worker |
| Testing | Pest feature tests |
| Development | Laravel Sail (Docker Compose), phpMyAdmin |
| Hosting | Wasmer |

---

## Good to know

* **Not for emergencies.** Reports are reviewed by staff and are not monitored in real time. In an emergency, call 911 or go to the barangay hall.
* **AI is only a helper.** It can suggest a category or an urgency level, but barangay staff always make the final decision.
* **Your privacy.** Read how your data is handled in the [Privacy Policy](https://civicguard.wasmer.app/privacy).

---

## Developed by

Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez
