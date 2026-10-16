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

* **Incident reporting:** pick a category, describe the problem, add a location, and optionally a photo and GPS pin
* **AI assistant:** a chat that helps residents write a report, with emergency warnings in English and Tagalog
* **AI suggestions:** automatic category and urgency rating, which staff can always change
* **Report tracking:** follow a report from Received to Assigned, In progress, and Resolved, with notifications
* **Confirm or reopen:** residents can confirm a fix or reopen a report within 7 days
* **Incident queue:** reports sorted by urgency, with tabs, search, and filters
* **Role-based access:** residents, tanods, officials, and admins, where tanods only see reports assigned to them
* **Curfew logging:** tanods can record curfew violations
* **Analytics:** charts, key numbers, and AI-written insights
* **Audit logs and PDF exports:** a record of who did what, and downloadable reports
* **Install as an app:** works on iPhone and Android from the browser, with an offline page
* **English and Tagalog**, with light and dark mode
* **Privacy tools:** account deletion, a privacy policy, and terms of use

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

**Backend**

* Laravel 12
* PHP 8.2+
* MySQL 8.4

**Frontend**

* Blade
* Alpine.js 3
* Tailwind CSS 4
* Vite 6
* Chart.js

**Services & tools**

* Google Gemini and Groq (automatic failover)
* DomPDF
* Laravel Sail (Docker)
* Wasmer (hosting)

---

## Good to know

* **Not for emergencies.** Reports are reviewed by staff and are not monitored in real time. In an emergency, call 911 or go to the barangay hall.
* **AI is only a helper.** It can suggest a category or an urgency level, but barangay staff always make the final decision.
* **Your privacy.** Read how your data is handled in the [Privacy Policy](https://civicguard.wasmer.app/privacy).

---

## Developed by

Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez
