# AGENTS.md

Guidance for any AI coding agent working in this repository (Codex, Copilot, Cursor, Gemini, and others). Human contributors can read it too.

CivicGuard is an AI-integrated incident and curfew monitoring system for Barangay Maimpis, City of San Fernando, Pampanga (capstone project). Residents report incidents, staff review and resolve them, and tanods log curfew violations. Live site: https://civicguard.wasmer.app

## Stack

- Backend: Laravel 12, PHP 8.2+, MySQL 8.4
- Frontend: Blade templates, Alpine.js 3, Tailwind CSS 4, Vite 6, Chart.js
- AI: Google Gemini and Groq with automatic failover (`app/Services/AiRouter.php`, clients in `GeminiClient.php` and `OpenAiCompatibleClient.php`). Order is set by `AI_PROVIDERS` (default `gemini,groq`)
- Auth scaffolding: Laravel Breeze. PDF: DomPDF
- Fonts: Figtree and Bitter, served locally from `public/fonts` (SIL OFL)
- Dev environment: Laravel Sail (Docker) on WSL2. Hosting: Wasmer (`app.yaml`)
- Tests: Pest. Formatting: Laravel Pint. Browser checks: Playwright scripts in `scripts/`

## Project layout

- `app/Http/Controllers`, `app/Http/Requests`, `app/Http/Middleware`
- `app/Models`: Report, CurfewLog, User, AuditLog, CaseAssignment, AppNotification
- `app/Services`: AI clients and router
- `resources/views`: Blade views grouped by feature (reports, curfew, admin, chatbot)
- `resources/lang`: English and Tagalog strings
- `resources/css/app.css`: Tailwind entry and shared motion tokens
- `routes/web.php`, `routes/auth.php`
- `tests/Feature`, `tests/Unit`
- `scripts/`: performance, mobile, accessibility, production-audit and end-to-end Playwright scripts
- `docs/`: `OPERATIONS.md`, `RA-9344-CURFEW-GUIDANCE.md`, screenshots
- `.github/workflows/`: `ci.yml`, `uptime.yml`; `.github/dependabot.yml`

## Commands

```bash
cp .env.example .env                       # then set a unique, non-empty DB_PASSWORD
composer install
./vendor/bin/sail up -d --build            # start the app (http://localhost, or set APP_PORT)
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed   # seeds report categories, not users
./vendor/bin/sail artisan civicguard:create-admin   # first admin; works only while none exists
npm ci
npm run dev                                # Vite dev server

./vendor/bin/sail artisan test             # Pest tests
./vendor/bin/sail bin pint                 # format PHP
vendor/bin/pint --test                     # formatter check (host PHP)
npm run build                              # production assets
npm run check:performance                  # compressed JS/CSS/landing/font budgets
npm run audit:mobile                       # needs: npx playwright install chromium
npm run audit:production                   # live-site audit; set PERF_URL for another deployment
npm run e2e:reports                        # resident-to-staff journey; isolated CI database only
```

CI (`ci.yml`) runs on pushes to `main` and pull requests: Pint check, tests, asset build, performance budgets, mobile and desktop layout checks, axe WCAG A/AA scans, and the e2e report journey. `uptime.yml` checks the production `/up` endpoint every 15 minutes.

## Workflow

- Start with a narrow search (`rg`, targeted globs, symbol lookup) before reading files. Read only the relevant line ranges; do not dump large files, generated assets, logs or CI transcripts.
- Reuse what is already established in the conversation. Do not reread unchanged files without a reason.
- Treat one reported bug or feature as one scope. Do not expand into an unfocused audit unless asked.
- For CI failures, inspect the failing assertion and a small surrounding log section first.
- Prefer existing helpers, components and patterns over parallel implementations. Follow the Laravel, Blade, Alpine.js, Tailwind and Pest patterns already in the repo. Do not add new tooling.
- Make the smallest complete change that fixes the root cause. In files with unrelated worktree changes, preserve them and edit only the requested area.
- Run the smallest relevant validation first (focused Pest test, Pint, Blade compilation, relevant frontend build). Escalate to broader tests only when that fails or the request requires it.
- Report changed files, validation results and any environment limitation briefly. Stop once the requested behavior is implemented and verified; do not generate speculative follow-up work.

## Conventions

- Commits follow Conventional Commits: `feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`. Lowercase, imperative, no trailing period.
- Indent 4 spaces, LF line endings (see `.editorconfig`). Run Pint before committing.
- All user-facing text must exist in both English and Tagalog (`resources/lang`).
- Every UI change must support light and dark mode (signed-in pages only; public and guest pages stay light), mobile layouts, accessibility, and role-based authorization.
- Never commit `.env`, API keys or secrets. Add new variables to `.env.example`. In production, secrets live in the hosting provider's settings and `APP_DEBUG=false`.

## Domain rules

- Roles: resident, tanod, official, admin. New sign-ups are always residents. Officials and tanods are created by an admin from the staff management page.
- Tanods only see reports assigned to them.
- AI only suggests category and urgency. Staff always make the final decision.
- Residents can confirm a fix or reopen a report within 7 days.
- Emergency guidance must keep directing people to call 911 and say that reports are not monitored in real time.
- There are no password-reset emails. Residents get a temporary password from authorized staff via the Reset Resident Passwords page; never write the new password to audit logs.
- Do not invent service promises, response-time guarantees or barangay contact information.
- Curfew records involve minors: record only what is necessary, describe observed facts without labeling the child, and never expose identifying details in public reports or screenshots. Follow `docs/RA-9344-CURFEW-GUIDANCE.md`. It is an operational aid, not legal advice.
- Uploads use the private `report_photos` disk (`PHOTO_DISK_ROOT=/data` in production). Verification proof images are deleted after staff decide. The app does not auto-purge reports, curfew records or audit logs; do not add a purge job until the barangay approves retention periods, and do not delete audit records ad hoc.

## Design rules

The full guide is `DESIGN.md`. Do not use it as permission to change routes, roles, report behavior, form fields or stored data. Key points:

- Calm, clear, dependable. Keep the CivicGuard logo and wordmark unchanged. Primary maroon `#951f3d`. Light canvas `#f6f2eb`, dark canvas `#1b1919`.
- Use red, amber, blue and green only for real severity, warning, info or success states. Avoid gradients, glows, glass effects and large shadows.
- Figtree for body, navigation, controls, data and signed-in headings; Bitter only for public editorial headings.
- 4px spacing base. Cards 12 to 14px radius, inputs and buttons 8px. Prefer whitespace and thin dividers over nested cards.
- Controls at least 44px high where practical, with visible keyboard focus and clear disabled, loading, error and success states.
- Use the shared motion tokens in `resources/css/app.css` (180ms feedback, 240ms enter, 140ms exit, 280ms page/reveal, `--cg-ease-out` / `--cg-ease-in`). Animate transform and opacity only, and respect `prefers-reduced-motion`.
- Keep semantic HTML, labels, skip links, live status messaging and sufficient contrast in both themes. Avoid horizontal page overflow at narrow widths. Keep the signed-in sidebar's tooltips, accessible names and mobile drawer.
- Landing page: keep resident registration and staff sign-in as distinct paths in `#get-started`, preserve `#how-it-works`, keep the 911 guidance prominent with the real-time monitoring warning beside it, work without JavaScript for the initial English content, and prefer an honest process explainer over fabricated dashboards or statistics.

## Operations

Details in `docs/OPERATIONS.md`.

- Health endpoint: `/up`. Slow requests are logged (threshold `SLOW_REQUEST_THRESHOLD_MS`, default 1500). Slack uptime alerts use the Actions secret `UPTIME_ALERT_WEBHOOK`.
- `app.yaml` holds Wasmer build and runtime settings, the persistent `/data` volume, and the post-deploy `php artisan migrate --force --no-interaction` job. View logs with `wasmer app logs miconvr/civicguard`.
- Back up the database and `/data` before schema or storage changes, and rehearse restores on a private staging environment.

## Other notes

- Repo has no open-source license. Authors: Mico Gerard Navarro, Franz Mikey Reyes, Carl Spencer Talon, Adriann Enriquez.
- Chat text and report descriptions may be sent to Gemini or Groq when keys are configured; the app works without AI keys.
