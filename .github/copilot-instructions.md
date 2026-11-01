# CivicGuard Copilot Guidelines

## Context efficiency

- Start with a narrow search (`rg`, targeted file globs, or symbol lookup) before reading files.
- Read only the relevant line ranges. Do not dump whole large files, generated assets, logs, or CI transcripts.
- Reuse information already established in the conversation. Do not reread unchanged files without a specific reason.
- Treat one reported bug or feature as one scope. Do not expand into an unfocused audit unless requested.
- For CI failures, inspect the failing assertion and a small surrounding log section first.
- Prefer existing helpers, components, and project patterns over introducing parallel implementations.

## Implementation workflow

- Make the smallest complete change that addresses the root cause.
- In files with unrelated worktree changes, preserve those changes and edit only the requested area.
- Run the smallest relevant validation command first: focused Pest tests, Pint, Blade compilation, or the relevant frontend build.
- Escalate to broader tests only when focused validation reveals a problem or the request requires it.
- Report changed files, validation results, and any environment limitation briefly.
- Stop after the requested behavior is implemented and verified; do not generate speculative follow-up work.

## CivicGuard requirements

- Follow Laravel, Blade, Alpine.js, Tailwind CSS, and Pest patterns already used in the repository.
- Keep user-facing text in both English and Tagalog.
- Preserve light/dark mode, mobile layouts, accessibility, and role-based authorization.
- Never expose secrets or commit `.env` files or API keys.
- Use the repository's existing documentation and test commands rather than adding new tooling.
