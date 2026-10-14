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

## UI motion

Pages use brief entrance and hover animations, and all motion respects `prefers-reduced-motion`. The dropdown and success-check patterns are adapted from [transitions.dev](https://transitions.dev/). Design notes are in [DESIGN.md](DESIGN.md).

---

## AI Features and Privacy

The AI Assistant, category suggestion, severity classification, and analytics insights use the Google Gemini API through `GEMINI_API_KEY`.

* **Without a key**, the app still works: severity uses keyword matching, the assistant answers from built-in FAQs, insights use rule-based summaries, and category suggestion stays empty.
* **With a key**, report text (descriptions and categories) is sent to Google for processing. Report content can include personal details, so check that this is acceptable for your barangay and mention it in your privacy notice.

---

## Scope and limitations

* Reports are not monitored in real time. For an emergency, call 911 or go to the barangay hall directly.
* The emergency guidance in the chat is a fixed warning. It does not replace contacting emergency services.
* The AI category suggestion is only a suggestion. Barangay staff review every report.
* CivicGuard is built for Barangay Maimpis and is not a general-purpose reporting system.

---

## Developed by

Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez
