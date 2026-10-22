import assert from 'node:assert/strict';
import { chromium } from 'playwright';
import { auditAccessibility } from './accessibility-audit.mjs';

const baseUrl = (process.env.E2E_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const password = 'CivicGuard-E2E-Only-2026!';
const description = 'A neighbor played loud music after midnight near the community hall.';
const location = 'Purok E2E, beside the community hall';
const browser = await chromium.launch({ headless: true });
const errors = [];

async function signIn(page, email) {
    await page.goto(`${baseUrl}/login`);
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: /log in/i }).click();
    await page.waitForURL('**/dashboard');
}

try {
    const residentContext = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });
    const residentPage = await residentContext.newPage();
    residentPage.on('pageerror', error => errors.push(error.message));
    await signIn(residentPage, 'resident.e2e@civicguard.test');

    await residentPage.goto(`${baseUrl}/reports/create`);
    await residentPage.getByRole('button', { name: /fill out the form myself/i }).click();
    const formAccessibility = await auditAccessibility(residentPage, 'resident report form');
    assert.equal(formAccessibility.blockingCount, 0, 'report form should have no serious/critical accessibility violations');
    await residentPage.locator('#description').fill(description);
    await residentPage.locator('#category_id').selectOption({ label: 'Public Disturbance' });
    await residentPage.locator('#location_text').fill(location);
    await residentPage.locator('input[name="latitude"]').fill('15.0612');
    await residentPage.locator('input[name="longitude"]').fill('120.6570');
    await residentPage.locator('#photo').setInputFiles({
        name: 'e2e-test.png',
        mimeType: 'image/png',
        buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC', 'base64'),
    });
    await residentPage.locator('#submit-report-btn').click();
    await residentPage.waitForURL('**/reports');
    await residentPage.getByText(description, { exact: true }).waitFor({ state: 'visible' });

    const officialContext = await browser.newContext({ viewport: { width: 1280, height: 900 } });
    const officialPage = await officialContext.newPage();
    officialPage.on('pageerror', error => errors.push(error.message));
    await signIn(officialPage, 'official.e2e@civicguard.test');
    await officialPage.goto(`${baseUrl}/admin/dashboard`);
    const staffAccessibility = await auditAccessibility(officialPage, 'official dashboard');
    assert.equal(staffAccessibility.blockingCount, 0, 'staff dashboard should have no serious/critical accessibility violations');
    const reportRow = officialPage.getByRole('row').filter({ hasText: location });
    await reportRow.click();
    const drawer = officialPage.getByRole('dialog', { name: 'Report details' });
    await drawer.waitFor({ state: 'visible' });
    await drawer.locator('img[alt="Incident photo"]').evaluate(image => image.decode());
    const drawerAccessibility = await auditAccessibility(officialPage, 'official report drawer');
    assert.equal(drawerAccessibility.blockingCount, 0, 'report drawer should have no serious/critical accessibility violations');
    await drawer.locator('select[name="assigned_to"]').selectOption({ label: 'E2E Tanod' });
    await drawer.locator('select[name="status"]').selectOption('in_progress');
    await drawer.getByRole('button', { name: 'Save changes' }).click();
    await officialPage.waitForURL('**/admin/dashboard**');

    const tanodContext = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });
    const tanodPage = await tanodContext.newPage();
    tanodPage.on('pageerror', error => errors.push(error.message));
    await signIn(tanodPage, 'tanod.e2e@civicguard.test');
    await tanodPage.goto(`${baseUrl}/admin/dashboard`);
    const tanodAccessibility = await auditAccessibility(tanodPage, 'tanod dashboard');
    assert.equal(tanodAccessibility.blockingCount, 0, 'tanod dashboard should have no serious/critical accessibility violations');
    await tanodPage.getByRole('row').filter({ hasText: location }).click();
    const tanodDrawer = tanodPage.getByRole('dialog', { name: 'Report details' });
    await tanodDrawer.waitFor({ state: 'visible' });
    await tanodDrawer.locator('select[name="status"]').selectOption('resolved');
    await tanodDrawer.getByRole('button', { name: 'Save changes' }).click();

    await residentPage.goto(`${baseUrl}/reports`);
    await residentPage.getByRole('button', { name: /yes, it's fixed/i }).click();
    await residentPage.getByText('Thank you for confirming.').waitFor({ state: 'visible' });

    assert.deepEqual(errors, [], 'browser pages should not report JavaScript errors');
    console.log('Resident report with photo and pin → official assignment → tanod resolution → resident confirmation: passed.');

    await residentContext.close();
    await officialContext.close();
    await tanodContext.close();
} finally {
    await browser.close();
}
