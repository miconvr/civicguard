import { chromium, devices } from 'playwright';

const baseUrl = (process.env.PERF_URL || 'https://civicguard.wasmer.app').replace(/\/$/, '');
const browser = await chromium.launch({ headless: true });
const failures = [];
const browserErrors = [];

try {
    const context = await browser.newContext({
        ...devices['iPhone 13'],
        reducedMotion: 'reduce',
    });
    const page = await context.newPage();
    page.on('pageerror', error => browserErrors.push(error.message));

    for (const path of ['/', '/login', '/privacy', '/terms', '/reports/create']) {
        const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'domcontentloaded' });
        const status = response?.status() ?? 0;

        if (status >= 400 || status === 0) {
            failures.push(`${path}: returned HTTP ${status}`);
            continue;
        }

        await page.waitForLoadState('networkidle', { timeout: 3000 }).catch(() => {});

        const checks = await page.evaluate(() => {
            const named = element => Boolean(
                element.getAttribute('aria-label')?.trim()
                || element.getAttribute('aria-labelledby')?.trim()
                || element.textContent?.trim()
                || element.querySelector('img[alt], svg[aria-label], svg[aria-labelledby]')
                || element.querySelector('[class*="sr-only"], [class*="visually-hidden"]'),
            );
            const labelsControl = control => {
                if (control.labels?.length) return true;
                if (control.getAttribute('aria-label')?.trim() || control.getAttribute('aria-labelledby')?.trim()) return true;
                return false;
            };
            const unlabeledControls = [...document.querySelectorAll('input, textarea, select')]
                .filter(control => !['hidden', 'submit', 'button', 'reset', 'image'].includes(control.type))
                .filter(control => !labelsControl(control))
                .length;
            const unnamedButtons = [...document.querySelectorAll('button, input[type="submit"], input[type="button"]')]
                .filter(button => !named(button) && !button.getAttribute('value')?.trim())
                .length;
            const unnamedLinks = [...document.querySelectorAll('a[href]')]
                .filter(link => !named(link))
                .length;
            const imagesWithoutAlt = [...document.querySelectorAll('img:not([alt])')].length;
            const overflow = document.documentElement.scrollWidth > window.innerWidth + 1;

            return {
                title: document.title,
                hasMain: Boolean(document.querySelector('main, [role="main"]')),
                unlabeledControls,
                unnamedButtons,
                unnamedLinks,
                imagesWithoutAlt,
                overflow,
                width: document.documentElement.scrollWidth,
                viewport: window.innerWidth,
            };
        });

        if (!checks.title) failures.push(`${path}: missing document title`);
        if (!checks.hasMain) failures.push(`${path}: missing main landmark`);
        if (checks.unlabeledControls) failures.push(`${path}: ${checks.unlabeledControls} form control(s) lack an accessible label`);
        if (checks.unnamedButtons) failures.push(`${path}: ${checks.unnamedButtons} button(s) lack an accessible name`);
        if (checks.unnamedLinks) failures.push(`${path}: ${checks.unnamedLinks} link(s) lack an accessible name`);
        if (checks.imagesWithoutAlt) failures.push(`${path}: ${checks.imagesWithoutAlt} image(s) lack alt text`);
        if (checks.overflow) failures.push(`${path}: horizontal overflow (${checks.width}px content in ${checks.viewport}px viewport)`);

        console.log(`${status} ${path} (${page.url()})`);
    }

    if (browserErrors.length) failures.push(...browserErrors.map(error => `Browser error: ${error}`));

    if (failures.length) {
        console.error('\nMobile audit failures:');
        failures.forEach(failure => console.error(`- ${failure}`));
        process.exitCode = 1;
    } else {
        console.log('\nMobile layout and accessibility smoke checks passed.');
    }

    await context.close();
} finally {
    await browser.close();
}
