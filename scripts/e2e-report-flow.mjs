import assert from 'node:assert/strict';
import { chromium } from 'playwright';
import { auditAccessibility } from './accessibility-audit.mjs';

const baseUrl = (process.env.E2E_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const password = 'CivicGuard-E2E-Only-2026!';
const description = 'A neighbor played loud music after midnight near the community hall.';
const location = 'Purok E2E, beside the community hall';
const browser = await chromium.launch({ headless: true });
const errors = new Set();

function captureBrowserErrors(page) {
    page.on('pageerror', error => errors.add(`${page.url()}\n${error.stack || error.message}`));
    page.on('console', message => {
        if (['error', 'warning'].includes(message.type()) && /Alpine Expression Error|Unexpected token/.test(message.text())) {
            errors.add(`${page.url()}\n${message.text()}`);
        }
    });
}

async function signIn(page, email) {
    await page.goto(`${baseUrl}/login`);
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: /log in/i }).click();
    await page.waitForURL('**/dashboard');
}

async function openAccountMenu(page) {
    const accountTrigger = page.locator('button[aria-controls="account-menu"]');

    // The account control lives at the bottom of the sidebar, which is closed on mobile.
    if (!(await accountTrigger.isVisible())) {
        await page.locator('.cg-sidebar-mobilebar button[aria-controls="primary-navigation"]').click();
        await accountTrigger.waitFor({ state: 'visible' });
    }

    await accountTrigger.click();
    await page.locator('#account-menu').waitFor({ state: 'visible' });

    return accountTrigger;
}

async function switchToTagalog(page) {
    await openAccountMenu(page);
    await page.locator('#account-menu').getByRole('link', { name: 'Tagalog' }).click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForFunction(() => document.documentElement.lang === 'tl');
}

async function inspectTagalogLayout(page) {
    return page.evaluate(() => {
        const visible = element => {
            const style = getComputedStyle(element);
            const rect = element.getBoundingClientRect();
            return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
        };
        const hasVisibleText = element => [...element.childNodes].some(node =>
            node.nodeType === Node.TEXT_NODE
                && node.textContent.trim()
                && !node.parentElement?.closest('.sr-only, [aria-hidden="true"]'),
        );
        const clippedLabels = [...document.querySelectorAll('button, a[href], [role="button"]')]
            .filter(element => visible(element) && !element.matches('button[aria-controls="account-menu"]'))
            .flatMap(element => [element, ...element.querySelectorAll('*')])
            .filter(element => visible(element)
                && !element.closest('.sr-only, [aria-hidden="true"]')
                && hasVisibleText(element))
            .filter(element => {
                const style = getComputedStyle(element);
                const isClipped = ['hidden', 'clip'].includes(style.overflowX)
                    || style.textOverflow === 'ellipsis'
                    || (style.webkitLineClamp && style.webkitLineClamp !== 'none')
                    || style.whiteSpace === 'nowrap';
                return isClipped && element.scrollWidth > element.clientWidth + 2;
            })
            .map(element => [...element.childNodes]
                .filter(node => node.nodeType === Node.TEXT_NODE && node.textContent.trim())
                .map(node => node.textContent.trim())
                .join(' ')
                .replace(/\s+/g, ' ')
                .slice(0, 100));

        return {
            language: document.documentElement.lang,
            contentWidth: document.documentElement.scrollWidth,
            viewportWidth: window.innerWidth,
            clippedLabels: [...new Set(clippedLabels)],
        };
    });
}

async function auditTagalogLayouts(page, paths, role) {
    const failures = [];
    const widths = [
        { name: 'mobile', width: 390 },
        { name: 'desktop', width: 1280 },
    ];

    for (const { name, width } of widths) {
        await page.setViewportSize({ width, height: 900 });

        for (const path of paths) {
            const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'domcontentloaded' });
            if (!response || response.status() >= 400) {
                failures.push(`${role} ${name} ${path}: returned HTTP ${response?.status() ?? 0}`);
                continue;
            }

            await page.evaluate(() => Promise.allSettled(
                [...document.querySelectorAll('[data-reveal]')]
                    .flatMap(element => element.getAnimations())
                    .map(animation => animation.finished),
            ));

            const recordLayout = async state => {
                const layout = await inspectTagalogLayout(page);
                if (layout.language !== 'tl') failures.push(`${role} ${name} ${path} ${state}: locale is ${layout.language || 'unset'}, expected tl`);
                if (layout.contentWidth > layout.viewportWidth + 1) {
                    failures.push(`${role} ${name} ${path} ${state}: horizontal overflow (${layout.contentWidth}px content in ${layout.viewportWidth}px viewport)`);
                }
                for (const label of layout.clippedLabels) failures.push(`${role} ${name} ${path} ${state}: clipped interactive label “${label}”`);
            };

            await recordLayout('page');

            if (path === paths[0] && await page.locator('button[aria-controls="account-menu"]').count()) {
                const accountMenu = await openAccountMenu(page);
                await recordLayout('account menu');
                await accountMenu.click();
            }
        }
    }

    return failures;
}

try {
    const residentContext = await browser.newContext({
        viewport: { width: 390, height: 844 },
        isMobile: true,
        hasTouch: true,
        permissions: ['geolocation'],
        geolocation: { latitude: 15.0612, longitude: 120.657 },
    });
    const residentPage = await residentContext.newPage();
    captureBrowserErrors(residentPage);
    await signIn(residentPage, 'resident.e2e@civicguard.test');

    await residentPage.goto(`${baseUrl}/reports/create`);
    await residentPage.getByRole('button', { name: /fill out the form myself/i }).click();
    await residentPage.locator('section[aria-label="Incident report form"]').evaluate(element =>
        Promise.allSettled(element.getAnimations().map(animation => animation.finished)),
    );
    await residentPage.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    await residentPage.waitForFunction(() => {
        const panel = document.querySelector('section[aria-label="Incident report form"]');
        return panel
            && Number(getComputedStyle(panel).opacity) >= 0.999
            && panel.getAnimations().every(animation => animation.playState === 'finished');
    });
    const formAccessibility = await auditAccessibility(residentPage, 'resident report form');
    assert.equal(formAccessibility.blockingCount, 0, 'report form should have no serious/critical accessibility violations');
    await residentPage.locator('#description').fill(description);
    await residentPage.locator('#category_id').selectOption({ label: 'Public Disturbance' });
    await residentPage.locator('#location_text').fill(location);
    await residentPage.getByRole('button', { name: 'Pin' }).click();
    await residentPage.waitForFunction(() => document.querySelector('#location-status')?.textContent?.startsWith('Location pinned.'));
    assert.equal(await residentPage.locator('input[name="latitude"]').inputValue(), '15.0612000');
    assert.equal(await residentPage.locator('input[name="longitude"]').inputValue(), '120.6570000');
    await residentPage.locator('#photo').setInputFiles({
        name: 'e2e-test.png',
        mimeType: 'image/png',
        buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC', 'base64'),
    });
    await residentPage.locator('#submit-report-btn').click();
    await residentPage.waitForURL('**/reports');
    const residentReport = residentPage.locator('article').filter({ hasText: location });
    await residentReport.locator('p.line-clamp-2').filter({ hasText: description }).waitFor({ state: 'visible' });

    const officialContext = await browser.newContext({ viewport: { width: 1280, height: 900 } });
    const officialPage = await officialContext.newPage();
    captureBrowserErrors(officialPage);
    await signIn(officialPage, 'official.e2e@civicguard.test');
    // Use the all-reports tab so this flow doesn't depend on the dashboard's
    // default active filter when checking the freshly submitted report.
    const staffDashboardResponse = await officialPage.goto(`${baseUrl}/admin/dashboard?tab=all`);
    assert.equal(staffDashboardResponse?.status(), 200, `staff dashboard should load, received HTTP ${staffDashboardResponse?.status() ?? 0}`);
    const staffAccessibility = await auditAccessibility(officialPage, 'official dashboard');
    assert.equal(staffAccessibility.blockingCount, 0, 'staff dashboard should have no serious/critical accessibility violations');
    const reportRows = officialPage.locator('table.cg-queue tbody tr');
    const reportRow = reportRows.filter({ hasText: location });
    try {
        await reportRow.waitFor({ state: 'visible', timeout: 10000 });
    } catch (error) {
        console.error('Report was not listed in the staff queue. Current queue rows:', await reportRows.allInnerTexts());
        console.error('Staff dashboard diagnostic:', {
            url: officialPage.url(),
            title: await officialPage.title(),
            body: (await officialPage.locator('body').innerText()).slice(0, 2000),
        });
        throw error;
    }
    await reportRow.click();
    const drawer = officialPage.getByRole('dialog', { name: 'Report details' });
    await drawer.waitFor({ state: 'visible' });
    await drawer.evaluate(element => Promise.allSettled(element.getAnimations().map(animation => animation.finished)));
    await drawer.locator('img[alt="Incident photo"]').evaluate(image => image.decode());
    const drawerAccessibility = await auditAccessibility(officialPage, 'official report drawer');
    assert.equal(drawerAccessibility.blockingCount, 0, 'report drawer should have no serious/critical accessibility violations');
    await drawer.locator('select[name="assigned_to"]').selectOption({ label: 'E2E Tanod' });
    await drawer.locator('select[name="status"]').selectOption('in_progress');
    const officialSaveResponse = officialPage.waitForResponse(response =>
        response.request().method() === 'POST'
            && /^\/admin\/reports\/[^/]+$/.test(new URL(response.url()).pathname),
    );
    const officialSaveNavigation = officialPage.waitForNavigation({ waitUntil: 'domcontentloaded' });
    await drawer.getByRole('button', { name: 'Save changes' }).click();
    const [officialResponse] = await Promise.all([officialSaveResponse, officialSaveNavigation]);
    assert.equal(officialResponse.status(), 302, `official report update should redirect successfully, received HTTP ${officialResponse.status()}`);
    await officialPage.getByRole('status').getByText(/Report changes saved\.|Na-save ang mga pagbabago sa ulat\./i).waitFor({ state: 'visible' });
    await officialPage.locator('table.cg-queue tbody tr').filter({ hasText: location }).getByText('E2E Tanod').waitFor({ state: 'visible' });

    const tanodContext = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });
    const tanodPage = await tanodContext.newPage();
    captureBrowserErrors(tanodPage);
    await signIn(tanodPage, 'tanod.e2e@civicguard.test');
    await tanodPage.goto(`${baseUrl}/admin/dashboard`);
    const tanodAccessibility = await auditAccessibility(tanodPage, 'tanod dashboard');
    assert.equal(tanodAccessibility.blockingCount, 0, 'tanod dashboard should have no serious/critical accessibility violations');
    await tanodPage.locator('table.cg-queue tbody tr').filter({ hasText: location }).click();
    const tanodDrawer = tanodPage.getByRole('dialog', { name: 'Report details' });
    await tanodDrawer.waitFor({ state: 'visible' });
    await tanodDrawer.locator('select[name="status"]').selectOption('resolved');
    const tanodSaveResponse = tanodPage.waitForResponse(response =>
        response.request().method() === 'POST'
            && /^\/admin\/reports\/[^/]+$/.test(new URL(response.url()).pathname),
    );
    const tanodSaveNavigation = tanodPage.waitForNavigation({ waitUntil: 'domcontentloaded' });
    await tanodDrawer.getByRole('button', { name: 'Save changes' }).click();
    const [tanodResponse] = await Promise.all([tanodSaveResponse, tanodSaveNavigation]);
    assert.equal(tanodResponse.status(), 302, `tanod report update should redirect successfully, received HTTP ${tanodResponse.status()}`);
    await tanodPage.getByRole('status').getByText(/Report changes saved\.|Na-save ang mga pagbabago sa ulat\./i).waitFor({ state: 'visible' });
    await tanodPage.goto(`${baseUrl}/admin/dashboard?tab=resolved`);
    const resolvedReportRow = tanodPage.locator('table.cg-queue tbody tr').filter({ hasText: location });
    await resolvedReportRow.getByText('Resolved', { exact: true }).waitFor({ state: 'visible' });

    await residentPage.goto(`${baseUrl}/reports`);
    const confirmationResponse = residentPage.waitForResponse(response =>
        response.request().method() === 'POST' && new URL(response.url()).pathname.endsWith('/confirm'),
    );
    const confirmationNavigation = residentPage.waitForNavigation({ waitUntil: 'domcontentloaded' });
    await residentPage.getByRole('button', { name: /yes, it's fixed/i }).click();
    const [response, confirmationPageResponse] = await Promise.all([confirmationResponse, confirmationNavigation]);
    assert.equal(response.status(), 302, `resident confirmation should redirect successfully, received HTTP ${response.status()}`);
    assert.equal(
        new URL(response.headers().location, baseUrl).pathname,
        '/reports',
        `resident confirmation should redirect to the reports page, received ${response.headers().location}`,
    );
    await response.finished();
    const confirmedReport = residentPage.locator('article').filter({ hasText: location });
    try {
        await confirmedReport.getByText(/Confirmed fixed|Kinumpirmang naayos na/i).waitFor({ state: 'visible', timeout: 10000 });
    } catch (error) {
        console.error('Resident confirmation did not appear after the redirect:', {
            url: residentPage.url(),
            language: await residentPage.locator('html').getAttribute('lang'),
            pageStatus: confirmationPageResponse?.status() ?? null,
            pageText: (await residentPage.locator('body').innerText()).slice(0, 2000),
            reportCard: (await confirmedReport.innerText().catch(() => 'Report card was not rendered')).slice(0, 2000),
        });
        throw error;
    }

    await switchToTagalog(residentPage);
    const residentTagalogFailures = await auditTagalogLayouts(
        residentPage,
        ['/dashboard', '/reports', '/reports/create', '/profile', '/notifications'],
        'resident',
    );

    await switchToTagalog(officialPage);
    const staffTagalogFailures = await auditTagalogLayouts(
        officialPage,
        ['/dashboard', '/admin/dashboard', '/admin/audit-logs', '/admin/verifications', '/admin/analytics', '/admin/reports/consolidated', '/admin/staff/create', '/notifications', '/profile'],
        'staff',
    );

    await officialPage.goto(`${baseUrl}/`);
    // The public header is intentionally hidden while the landing hero is in view.
    // Use the visible language toggle in the hero strip instead.
    await officialPage.locator('.cg-hero-strip button[aria-pressed]').click();
    await officialPage.waitForFunction(() => document.documentElement.lang === 'tl');
    const publicTagalogFailures = await auditTagalogLayouts(officialPage, ['/'], 'public');

    await switchToTagalog(tanodPage);
    const tanodTagalogFailures = await auditTagalogLayouts(
        tanodPage,
        ['/dashboard', '/admin/dashboard', '/curfew/create', '/notifications', '/profile'],
        'tanod',
    );

    const tagalogLayoutFailures = [...residentTagalogFailures, ...staffTagalogFailures, ...publicTagalogFailures, ...tanodTagalogFailures];
    if (tagalogLayoutFailures.length) {
        console.error('\nTagalog layout audit failures:');
        tagalogLayoutFailures.forEach(failure => console.error(`- ${failure}`));
    }

    assert.deepEqual([...errors], [], 'browser pages should not report JavaScript errors');
    assert.deepEqual(tagalogLayoutFailures, [], 'Tagalog layouts should not overflow or clip interactive labels');
    console.log('Resident report with photo and pin → official assignment → tanod resolution → resident confirmation: passed.');

    await residentContext.close();
    await officialContext.close();
    await tanodContext.close();
} finally {
    await browser.close();
}
