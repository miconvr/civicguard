# CivicGuard Design System

## Design intent

CivicGuard is a public-service reporting tool for residents and barangay staff in Barangay Maimpis. The interface should feel calm, clear, and dependable. Take cues from warm editorial layouts and precise product interfaces, while keeping CivicGuard's own name, shield mark, maroon identity, and community-focused language.

Use this document as visual guidance, not as permission to change routes, roles, report behavior, form fields, or stored data.

## Brand and theme

- Keep the CivicGuard logo and wordmark unchanged.
- Keep maroon `#951f3d` as the primary action and selected-state color.
- Light theme canvas: `#f6f2eb`, with near-paper surfaces, warm raised surfaces, and restrained borders.
- Dark theme canvas: `#1b1919`. Use charcoal surfaces and the same maroon identity without turning the whole screen red.
- Use red, amber, blue, and green only when communicating real severity, warning, information, or success states.
- Avoid gradients, decorative glows, glass effects, and large shadows.

## Typography

- Use Figtree for body copy, navigation, controls, labels, data, and signed-in application headings.
- Use Bitter selectively for public editorial headings. Keep operational screens in Figtree.
- Keep headings concise and balanced. Use readable line lengths for prose, and compact typography only where users scan operational data.
- Do not use display typography for form controls, table data, or navigation labels.

## Shape, spacing, and layout

- Use a 4px spacing base, with 8px increments for repeated layout rhythm.
- Cards use a 12 to 14px radius and should express useful grouping, not decorate every block.
- Inputs and buttons use compact 8px radii. Small status tags may be pills.
- Prefer open layout, whitespace, and thin dividers over nested cards and ornamental borders.
- Keep the established public header, role-based navigation, page routes, and resident reporting flow familiar.
- The signed-in sidebar may collapse to an icon rail on desktop. Preserve visible tooltips, accessible link names, and the full mobile drawer.
- Keep appearance and language preferences in the signed-in account menu; public pages remain light-only.
- Make dense staff surfaces scannable. Keep urgent reports and primary actions easy to find.

## Interaction and motion

- Keep controls at least 44px high when practical, with visible keyboard focus and clear disabled, loading, error, and success states.
- Motion should clarify an entrance, reveal, or action response. Keep routine interactions quick and subtle.
- Use the shared motion tokens in `resources/css/app.css`: 180ms for interaction feedback, 240ms for enter transitions, 140ms for exits, 280ms for page/reveal motion, and the shared `--cg-ease-out` / `--cg-ease-in` curves. Tailwind transition utilities inherit the same defaults.
- Keep chatbot message, review, photo, and success transitions on those shared timings. Distinctive looping motion is reserved for status indicators and the public install walkthrough illustration.
- The public hero may use a single slow contour-ring animation, staggered copy entrance, and ordered process reveals. Keep routine page motion brief and avoid continuous motion elsewhere.
- Use transform and opacity for movement. Respect `prefers-reduced-motion`; content must remain visible and fully usable without animation.
- Do not make keyboard navigation depend on hover or animation.
- Prefer progressive disclosure for optional guidance, such as report-writing tips.

### Motion recipes

Pick one of these instead of inventing a new animation. Timings are the tokens in `resources/css/app.css` (`:root`).

- **Page and card entrance:** `data-reveal` on cards (staggered fade and 12px rise) and `cg-page-enter` on `<main>`.
- **Side panel (staff report sidebar):** eases in 24px from the right with `--cg-motion-interaction` and out with `--cg-motion-exit`. The dim behind it fades with the same timings. Its sections rise 4px with `.cg-drawer-section` and `style="--i: n"`, 30ms apart.
- **Show or hide a block (summary banner, follow-up notes):** `x-transition` fade plus 4px rise, `--cg-motion-enter` in and `--cg-motion-exit` out.
- **Dropdown that expands in place (decline reason):** grid-rows `0fr` to `1fr` with `--cg-motion-interaction`, plus a chevron that rotates with the same timing.
- **Buttons and hover:** `--cg-motion-interaction`, 1px lift on `.cg-btn`, `.cg-btn-secondary` and `.cg-motion-lift`.
- **Contrast audits:** content inside a panel that opens on click must move with `transform` only. Fading text is read mid-fade by the accessibility check and fails it.
- **Overlay dim:** a light dim with a 2px blur, the same recipe as the mobile menu overlay. It covers the whole page, including the left nav.

## Accessibility and responsive behavior

- Preserve semantic HTML, labels, descriptive link text, live status messaging, skip links, and keyboard access.
- Maintain readable text and control contrast in both themes.
- Reflow timelines, tables, and navigation at narrow widths without horizontal page overflow.
- Do not hide important instructions or report status when motion is reduced or scripts are unavailable.

## Product and content constraints

- Preserve all existing features, routes, form names, role permissions, and data.
- Keep English and Tagalog behavior consistent when changing visible copy.
- Do not invent service promises, response-time guarantees, or barangay contact information.
- Emergency guidance must continue to direct people to call 911 and clarify that reports are not monitored in real time.

## Public landing page

- Keep resident registration and staff sign-in as distinct paths in the `#get-started` section.
- Preserve the `#how-it-works` anchor and describe the existing report, staff review, and status-update flow.
- Keep the 911 guidance prominent and directly actionable, with the real-time monitoring warning beside it.
- Keep the English/Tagalog control accessible without relying on JavaScript for the initial English content.
- Keep public and guest pages in the light cream theme; reserve the light/dark switch for signed-in app pages.
- Prefer an honest process explainer over fabricated dashboard previews, statistics, or local imagery.
- On the landing page the public header is hidden over the hero (a small language and sign-in strip sits in the hero corner) and slides in once the visitor scrolls past the hero. It stays visible without JavaScript and appears on keyboard focus.
