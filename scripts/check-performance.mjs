import { gzipSync } from 'node:zlib';
import { readFileSync, statSync } from 'node:fs';
import { resolve } from 'node:path';

const buildRoot = resolve('public/build');
const manifest = JSON.parse(readFileSync(resolve(buildRoot, 'manifest.json'), 'utf8'));
const failures = [];

function asset(entry) {
    return readFileSync(resolve(buildRoot, entry.file));
}

function report(label, bytes, limit, unit = 'gzip') {
    const passed = bytes <= limit;
    console.log(`${passed ? 'PASS' : 'FAIL'} ${label}: ${(bytes / 1024).toFixed(1)} KiB ${unit} (limit ${(limit / 1024).toFixed(0)} KiB)`);
    if (!passed) failures.push(label);
}

function gzipSize(entry) {
    return gzipSync(asset(entry), { level: 9 }).byteLength;
}

const app = manifest['resources/js/app.js'];
const css = manifest['resources/css/app.css'];
const cssSource = readFileSync(resolve(buildRoot, css.file), 'utf8');
const analyticsView = readFileSync(resolve('resources/views/admin/analytics.blade.php'), 'utf8');
const dynamicImports = new Set(app.dynamicImports ?? []);
const requiredOnDemand = [
    'resources/js/pages/landing.js',
    'resources/js/pages/profile-photo.js',
    'resources/js/pages/verification-tabs.js',
];

report('Shared JavaScript', gzipSize(app), 26 * 1024);
report('Shared CSS', gzipSize(css), 32 * 1024);

if (cssSource.includes('fonts.bunny.net') || analyticsView.includes('cdnjs.cloudflare.com')) {
    failures.push('third-party CSS and chart scripts must stay off the page');
} else {
    console.log('PASS No third-party font stylesheet or chart script');
}

for (const path of requiredOnDemand) {
    if (!dynamicImports.has(path)) failures.push(`${path} must remain page-loaded`);
}

const landing = manifest['resources/js/pages/landing.js'];
report('Landing page JavaScript chunk', gzipSize(landing), 7 * 1024);

const fontFiles = Object.values(manifest)
    .filter(entry => entry.file?.endsWith('.woff2'))
    .map(entry => resolve(buildRoot, entry.file));
if (fontFiles.length !== 5) failures.push(`expected 5 bundled font weights, found ${fontFiles.length}`);
const fontBytes = fontFiles.reduce((total, file) => total + statSync(file).size, 0);
report('Bundled WOFF2 fonts', fontBytes, 80 * 1024, 'transfer');

if (failures.length) {
    console.error(`Performance budget failed: ${failures.join(', ')}`);
    process.exitCode = 1;
} else {
    console.log('Performance budgets passed.');
}
