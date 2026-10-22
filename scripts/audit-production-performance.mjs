import { chromium } from 'playwright';

const url = process.env.PERF_URL ?? 'https://civicguard.wasmer.app/';
const browser = await chromium.launch({ headless: true, args: ['--no-sandbox'] });
const context = await browser.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 1,
    isMobile: true,
    hasTouch: true,
    reducedMotion: 'reduce',
});
const page = await context.newPage();
const failedRequests = [];
const pageErrors = [];
const assetHeaderChecks = [];
page.on('requestfailed', request => failedRequests.push({ url: request.url(), error: request.failure()?.errorText }));
page.on('pageerror', error => pageErrors.push(error.message));
page.on('response', response => {
    const pathname = new URL(response.url()).pathname;
    if (!/^\/build\/assets\/[A-Za-z0-9_-]+-[A-Za-z0-9_-]+\.(css|js|woff2)$/.test(pathname)) return;

    assetHeaderChecks.push(response.allHeaders().then(headers => {
        const cacheControl = headers['cache-control'] ?? '';
        if (!cacheControl.includes('max-age=31536000') || !cacheControl.includes('immutable')) {
            return `${pathname}: expected one-year immutable caching, received "${cacheControl || 'no Cache-Control header'}"`;
        }
        return null;
    }).catch(error => `${pathname}: unable to read response headers (${error.message})`));
});

const client = await context.newCDPSession(page);
await client.send('Network.enable');
await client.send('Network.emulateNetworkConditions', {
    offline: false,
    latency: 180,
    downloadThroughput: 200 * 1024,
    uploadThroughput: 90 * 1024,
    connectionType: 'cellular3g',
});
await client.send('Emulation.setCPUThrottlingRate', { rate: 4 });

await page.addInitScript(() => {
    window.__cgPerf = { lcp: 0, cls: 0 };
    try {
        new PerformanceObserver(list => {
            const entries = list.getEntries();
            if (entries.length) window.__cgPerf.lcp = entries.at(-1).startTime;
        }).observe({ type: 'largest-contentful-paint', buffered: true });
        new PerformanceObserver(list => {
            window.__cgPerf.cls += list.getEntries()
                .filter(entry => !entry.hadRecentInput)
                .reduce((sum, entry) => sum + entry.value, 0);
        }).observe({ type: 'layout-shift', buffered: true });
    } catch {}
});

let response;
let result;
let cacheHeaderIssues = [];
try {
    response = await page.goto(url, { waitUntil: 'load', timeout: 60_000 });
    await page.waitForTimeout(1_000);
    result = await page.evaluate(() => {
        const navigation = performance.getEntriesByType('navigation')[0];
        return {
            title: document.title,
            viewport: `${innerWidth}x${innerHeight}`,
            ttfbMs: Math.round(navigation?.responseStart ?? 0),
            domContentLoadedMs: Math.round(navigation?.domContentLoadedEventEnd ?? 0),
            loadMs: Math.round(navigation?.loadEventEnd ?? 0),
            lcpMs: Math.round(window.__cgPerf.lcp),
            cls: Number(window.__cgPerf.cls.toFixed(4)),
        };
    });
    cacheHeaderIssues = (await Promise.all(assetHeaderChecks)).filter(Boolean);
} finally {
    await context.close();
    await browser.close();
}

console.log(JSON.stringify({ url, status: response?.status(), throttling: '180 ms RTT, 1.6 Mbps down, 4× CPU', ...result, failedRequests, pageErrors, cacheHeaderIssues }, null, 2));

const failed = response?.status() !== 200
    || !result?.lcpMs
    || result.lcpMs > 7_000
    || result.cls > 0.1
    || failedRequests.length > 0
    || pageErrors.length > 0
    || cacheHeaderIssues.length > 0;

if (failed) process.exitCode = 1;
