# CivicGuard

AI-integrated incident and curfew monitoring system for Barangay Maimpis,
City of San Fernando, Pampanga. Residents report incidents, staff review and
resolve them, and tanods log curfew violations.

## Stack

- Backend: Laravel 12, PHP 8.2+, MySQL 8.4
- Frontend: Blade templates, Alpine.js 3, Tailwind CSS 4, Vite 6, Chart.js
- AI: Google Gemini and Groq with automatic failover (`app/Services/AiRouter.php`)
- PDF: DomPDF
- Dev environment: Laravel Sail (Docker) on WSL2
- Tests: Pest. Formatting: Laravel Pint

## Project layout

- `app/Http/Controllers`, `app/Http/Requests`, `app/Http/Middleware`
- `app/Models`: Report, CurfewLog, User, AuditLog, CaseAssignment, AppNotification
- `app/Services`: AI clients and router
- `resources/views`: Blade views grouped by feature (reports, curfew, admin, chatbot)
- `resources/lang`: English and Tagalog strings
- `routes/web.php`, `routes/auth.php`
- `tests/Feature`, `tests/Unit`

## Commands

```bash
./vendor/bin/sail up -d              # start the app
./vendor/bin/sail artisan migrate    # run migrations
./vendor/bin/sail artisan test       # run Pest tests
./vendor/bin/sail bin pint           # format PHP
npm run dev                          # Vite dev server
```

## Conventions

- Commits follow Conventional Commits: `feat:`, `fix:`, `docs:`, `chore:`,
  `refactor:`, `test:`. Lowercase, imperative, no trailing period.
- Indent 4 spaces, LF line endings (see `.editorconfig`). Run Pint before committing.
- All user-facing text must exist in both English and Tagalog.
- Support light and dark mode and mobile layouts for every UI change.
- Never commit `.env` or API keys. Add new variables to `.env.example`.

## Domain rules

- Roles: resident, tanod, official, admin. New sign-ups are always residents.
- Tanods only see reports assigned to them.
- AI only suggests category and urgency. Staff always make the final decision.
- Residents can confirm a fix or reopen a report within 7 days.
- Curfew guidance follows `docs/RA-9344-CURFEW-GUIDANCE.md`.
