/**
 * Screenshots aller Seiten in Desktop- und Handybreite, ganze Seite plus je Sektion.
 * Läuft im Playwright-Container (siehe tools/screenshot.sh). Ausgabe: tools/screenshots/
 * Aufruf: node screenshot.mjs [base-url] [nur-slug]
 */
import { chromium } from 'playwright';
import fs from 'node:fs';

const base = process.argv[2] || 'http://localhost:8092';
const only = process.argv[3] || '';
const pages = [
	{ slug: 'start', path: '/' },
	{ slug: 'voranmeldung', path: '/voranmeldung/' },
	{ slug: 'golfplaetze', path: '/golfplaetze/' },
	{ slug: 'unterstuetzen', path: '/unterstuetzen/' },
	{ slug: 'ueber-uns', path: '/ueber-uns/' },
	{ slug: 'impressum', path: '/impressum/' },
	{ slug: '404', path: '/gibt-es-nicht/' },
];
const viewports = [
	{ name: 'desktop', width: 1440, height: 900 },
	{ name: 'mobile', width: 390, height: 844, mobile: true },
];
const out = new URL('./screenshots/', import.meta.url).pathname;
fs.mkdirSync(out, { recursive: true });
const review = '/w/../.impeccable/review/';
fs.mkdirSync(review, { recursive: true });

const browser = await chromium.launch();
for (const vp of viewports) {
	const ctx = await browser.newContext({
		viewport: { width: vp.width, height: vp.height },
		deviceScaleFactor: 1,
		isMobile: !!vp.mobile,
		hasTouch: !!vp.mobile,
		reducedMotion: 'reduce',
		locale: 'de-DE',
	});
	for (const p of pages) {
		if (only && p.slug !== only) continue;
		const page = await ctx.newPage();
		const errors = [];
		page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
		page.on('pageerror', (e) => errors.push(String(e)));
		await page.goto(base + p.path, { waitUntil: 'networkidle' });
		await page.evaluate(() => document.querySelectorAll('[data-fwg-reveal]').forEach((el) => el.classList.add('is-in')));
		await page.waitForTimeout(400);
		const metrics = await page.evaluate(() => {
			const over = [...document.querySelectorAll('body *')].filter((el) => {
				const b = el.getBoundingClientRect();
				return b.width > 0 && (b.right > innerWidth + 1 || b.left < -1);
			}).slice(0, 10).map((el) => el.tagName + '.' + String(el.className || '').slice(0, 40) + ' r=' + Math.round(el.getBoundingClientRect().right));
			const small = [...document.querySelectorAll('a,button')].filter((el) => {
				const b = el.getBoundingClientRect();
				return b.width > 0 && b.height > 0 && (b.width < 24 || b.height < 24);
			}).map((el) => el.tagName + '.' + String(el.className || '').slice(0, 30) + ' ' + Math.round(el.getBoundingClientRect().width) + 'x' + Math.round(el.getBoundingClientRect().height));
			return { scrollW: document.scrollingElement.scrollWidth, innerW: innerWidth, docH: document.scrollingElement.scrollHeight, over, small, h1: document.querySelectorAll('h1').length };
		});
		await page.screenshot({ path: `${out}${p.slug}-${vp.name}.png`, fullPage: true });
		if (p.slug === 'start') {
			await page.screenshot({ path: `${review}${vp.name}.png`, fullPage: true });
		}
		const sections = await page.$$('header.fwg-header, .fwg-hero, .fwg-pagehead, main section, footer');
		let i = 0;
		for (const s of sections) {
			i++;
			try {
				await s.scrollIntoViewIfNeeded();
				await page.waitForTimeout(150);
				await s.screenshot({ path: `${out}${p.slug}-${vp.name}-${String(i).padStart(2, '0')}.png` });
			} catch (e) { /* unsichtbare Sektion */ }
		}
		console.log(`${p.slug} ${vp.name}: ${sections.length} Sektionen, ${metrics.docH}px hoch, scrollW ${metrics.scrollW}/${metrics.innerW}, h1=${metrics.h1}, overflow=${JSON.stringify(metrics.over)}, klein=${JSON.stringify(metrics.small)}, konsole=${JSON.stringify(errors)}`);
		await page.close();
	}
	await ctx.close();
}
await browser.close();
