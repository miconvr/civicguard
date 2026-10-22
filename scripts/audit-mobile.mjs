import { chromium, devices } from 'playwright';
import { auditAccessibility } from './accessibility-audit.mjs';

const baseUrl = (process.env.PERF_URL || 'https://civicguard.wasmer.app').replace(/\/$/, '');
const browser = await chromium.launch({ headless: true });
const failures = [];
const browserErrors = [];

try {
    const viewports = [
        { name: 'mobile', options: devices['iPhone 13'] },
        { name: 'desktop', options: { viewport: { width: 1280, height: 900 } } },
    ];

    for (const viewport of viewports) {
        const context = await browser.newContext({ ...viewport.options, reducedMotion: 'reduce' });
        const page = await context.newPage();
        page.on('pageerror', error => browserErrors.push(error.message));

        for (const path of ['/', '/login', '/register', '/privacy', '/terms']) {
            const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'domcontentloaded' });
            const status = response?.status() ?? 0;

            if (status >= 400 || status === 0) {
                failures.push(`${viewport.name} ${path}: returned HTTP ${status}`);
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
                const labelsControl = control => Boolean(
                    control.labels?.length
                    || control.getAttribute('aria-label')?.trim()
                    || control.getAttribute('aria-labelledby')?.trim(),
                );
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
                    language: document.documentElement.lang,
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

            if (!checks.title) failures.push(`${viewport.name} ${path}: missing document title`);
            if (!checks.language) failures.push(`${viewport.name} ${path}: missing document language`);
            if (!checks.hasMain) failures.push(`${viewport.name} ${path}: missing main landmark`);
            if (checks.unlabeledControls) failures.push(`${viewport.name} ${path}: ${checks.unlabeledControls} form control(s) lack an accessible label`);
            if (checks.unnamedButtons) failures.push(`${viewport.name} ${path}: ${checks.unnamedButtons} button(s) lack an accessible name`);
            if (checks.unnamedLinks) failures.push(`${viewport.name} ${path}: ${checks.unnamedLinks} link(s) lack an accessible name`);
            if (checks.imagesWithoutAlt) failures.push(`${viewport.name} ${path}: ${checks.imagesWithoutAlt} image(s) lack alt text`);
            if (checks.overflow) failures.push(`${viewport.name} ${path}: horizontal overflow (${checks.width}px content in ${checks.viewport}px viewport)`);

            if (path === '/') {
                await page.keyboard.press('Tab');
                const firstTabStopVisible = await page.evaluate(() => {
                    const active = document.activeElement;
                    const bounds = active.getBoundingClientRect();
                    return active !== document.body && bounds.width > 0 && bounds.height > 0;
                });
                if (!firstTabStopVisible) failures.push(`${viewport.name} /: keyboard focus did not reach a visible control`);
            }

            const accessibility = await auditAccessibility(page, `${viewport.name} ${path}`);
            if (accessibility.blockingCount) failures.push(`${viewport.name} ${path}: ${accessibility.blockingCount} serious/critical WCAG accessibility violation(s)`);

            console.log(`${viewport.name} ${status} ${path} (${page.url()})`);
        }

        await context.close();
    }

    if (browserErrors.length) failures.push(...browserErrors.map(error => `Browser error: ${error}`));

    if (failures.length) {
        console.error('\nMobile audit failures:');
        failures.forEach(failure => console.error(`- ${failure}`));
        process.exitCode = 1;
    } else {
        console.log('\nMobile layout and accessibility smoke checks passed.');
    }

} finally {
    await browser.close();
}
