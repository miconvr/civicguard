import axe from 'axe-core';

export async function auditAccessibility(page, label) {
    if (! await page.evaluate(() => Boolean(window.axe))) {
        await page.addScriptTag({ content: axe.source });
    }
    const results = await page.evaluate(() => window.axe.run(document, {
        runOnly: {
            type: 'tag',
            values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22a', 'wcag22aa'],
        },
    }));

    for (const violation of results.violations) {
        const detail = `${label}: ${violation.id} (${violation.impact}) — ${violation.help}`;
        if (['critical', 'serious'].includes(violation.impact)) {
            console.error(`A11Y FAIL ${detail}`);
        } else {
            console.warn(`A11Y REVIEW ${detail}`);
        }
        for (const node of violation.nodes) {
            console.error(`  ${node.target.join(', ')}`);
        }
    }

    const blocking = results.violations.filter(violation => ['critical', 'serious'].includes(violation.impact));
    return { violations: results.violations, blockingCount: blocking.length };
}
