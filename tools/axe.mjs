/**
 * Barrierefreiheits-Prüfung: axe-core plus skriptgesteuerte manuelle Checks.
 * Läuft im Playwright-Container (siehe tools/axe.sh). Ausgabe: stdout (Textprotokoll).
 * Aufruf: node axe.mjs [base-url]
 * Hinweis: Der Formular-Fehlerpfad wird mit abgefangenem Netzwerk (page.route) geprüft,
 * es wird nichts an den Server geschickt.
 */
import { chromium } from 'playwright';
import AxeBuilder from '@axe-core/playwright';

const base = process.argv[2] || 'http://localhost:8092';
const pages = ['/', '/voranmeldung/', '/golfplaetze/', '/unterstuetzen/', '/ueber-uns/', '/impressum/', '/barrierefreiheit/', '/datenschutz/', '/agb/', '/gibt-es-nicht/'];
const log = (...a) => console.log(...a);
const reveal = (page) => page.evaluate(() => document.querySelectorAll('[data-fwg-reveal]').forEach((el) => el.classList.add('is-in')));

const describeActive = () => {
	const el = document.activeElement;
	if (!el) return 'none';
	const cs = getComputedStyle(el);
	const r = el.getBoundingClientRect();
	const name = el.getAttribute('aria-label') || (el.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 40) || el.getAttribute('placeholder') || el.id;
	let ring = `outline=${cs.outlineStyle}/${cs.outlineWidth}/${cs.outlineColor}`;
	if (cs.boxShadow && cs.boxShadow !== 'none') ring += ` shadow=${cs.boxShadow.slice(0, 60)}`;
	ring += ` border=${cs.borderColor}`;
	// Radio-Pills: Fokusstil liegt auf dem Label
	if (el.matches('.fwg-radio input')) {
		const l = getComputedStyle(el.closest('.fwg-radio'));
		ring += ` labelShadow=${l.boxShadow.slice(0, 60)} labelOutline=${l.outlineStyle}/${l.outlineWidth}`;
	}
	const hidden = el.closest('[hidden]') ? ' IN-HIDDEN' : '';
	const inView = r.bottom >= 0 && r.top <= innerHeight && r.right >= 0 && r.left <= innerWidth ? '' : ' OFFSCREEN';
	return `${el.tagName.toLowerCase()}${el.className ? '.' + String(el.className).split(' ')[0] : ''} "${name}" ${Math.round(r.width)}x${Math.round(r.height)} ${ring} opacity=${cs.opacity}${hidden}${inView}`;
};

const browser = await chromium.launch();

/* ---- 1) axe-core je Seite, 1280 und 360 ---- */
for (const vp of [{ n: 'desktop', w: 1280, h: 800 }, { n: 'mobile', w: 360, h: 780, mobile: true }]) {
	const ctx = await browser.newContext({ viewport: { width: vp.w, height: vp.h }, isMobile: !!vp.mobile, hasTouch: !!vp.mobile, locale: 'de-DE' });
	for (const p of pages) {
		const page = await ctx.newPage();
		try {
			await page.goto(base + p, { waitUntil: 'networkidle' });
			await reveal(page);
			const res = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice']).analyze();
			log(`AXE ${vp.n} ${p}: ${res.violations.length} Verstöße, ${res.incomplete.length} unklar`);
			for (const v of res.violations) log(`  [${v.impact}] ${v.id} (${v.tags.filter((t) => /wcag\d/.test(t)).join(',')}): ${v.help} -> ${v.nodes.slice(0, 5).map((n) => n.target.join(' ')).join(' | ')}`);
			for (const v of res.incomplete) log(`  ? ${v.id}: ${v.nodes.slice(0, 4).map((n) => n.target.join(' ') + (n.any[0] && n.any[0].data && n.any[0].data.contrastRatio ? ' cr=' + n.any[0].data.contrastRatio : '')).join(' | ')}`);
		} catch (e) { log(`AXE ${vp.n} ${p}: FEHLER ${e.message}`); }
		await page.close();
	}
	await ctx.close();
}

/* ---- 2) Tastatur: Tab-Reihenfolge und Fokus-Stil (Desktop), Skip-Link ---- */
{
	const ctx = await browser.newContext({ viewport: { width: 1280, height: 800 }, locale: 'de-DE' });
	for (const p of ['/', '/voranmeldung/', '/golfplaetze/', '/unterstuetzen/']) {
		const page = await ctx.newPage();
		await page.goto(base + p, { waitUntil: 'networkidle' });
		await reveal(page);
		log(`\nTAB desktop ${p}`);
		await page.keyboard.press('Tab');
		log('  1: ' + await page.evaluate(describeActive) + ' skipTop=' + await page.evaluate(() => getComputedStyle(document.activeElement).top));
		if (p === '/') {
			await page.keyboard.press('Enter');
			await page.waitForTimeout(200);
			log('  Skip-Link Enter -> hash=' + await page.evaluate(() => location.hash) + ' active=' + await page.evaluate(describeActive));
			await page.keyboard.press('Tab');
			log('  Tab nach Skip -> ' + await page.evaluate(describeActive));
			await page.goto(base + p, { waitUntil: 'networkidle' });
			await reveal(page);
			await page.keyboard.press('Tab');
		}
		const seen = new Set();
		for (let i = 2; i <= 90; i++) {
			await page.keyboard.press('Tab');
			const d = await page.evaluate(describeActive);
			if (seen.has(d) && d.startsWith('a.fwg-skip')) { log('  (Ende, wieder beim Skip-Link)'); break; }
			seen.add(d);
			log(`  ${i}: ${d}`);
			if (d.startsWith('body')) { log('  (Fokus auf body)'); break; }
		}
		await page.close();
	}
	await ctx.close();
}

/* ---- 3) Mobile-Menü (360 px): Fokus, Falle, Escape, inert ---- */
{
	const ctx = await browser.newContext({ viewport: { width: 360, height: 780 }, isMobile: true, hasTouch: true, locale: 'de-DE' });
	const page = await ctx.newPage();
	await page.goto(base + '/', { waitUntil: 'networkidle' });
	log('\nMENU mobile /');
	await page.focus('[data-fwg-menu-open]');
	await page.keyboard.press('Enter');
	await page.waitForTimeout(150);
	log('  nach Öffnen: ' + await page.evaluate(describeActive));
	log('  menu attrs: ' + await page.evaluate(() => { const m = document.getElementById('fwg-menu'); return `hidden=${m.hidden} role=${m.getAttribute('role')} aria-modal=${m.getAttribute('aria-modal')} label=${m.getAttribute('aria-label')}; main inert=${document.querySelector('main').inert} main aria-hidden=${document.querySelector('main').getAttribute('aria-hidden')} header inert=${document.querySelector('header').inert} burger expanded=${document.querySelector('[data-fwg-menu-open]').getAttribute('aria-expanded')}`; }));
	for (let i = 1; i <= 10; i++) { await page.keyboard.press('Tab'); log(`  Tab ${i}: ` + await page.evaluate(describeActive)); }
	await page.keyboard.press('Shift+Tab'); await page.keyboard.press('Shift+Tab');
	log('  2x Shift+Tab: ' + await page.evaluate(describeActive));
	log('  aria-snapshot Menü:\n' + (await page.locator('#fwg-menu').ariaSnapshot()).split('\n').map((l) => '    ' + l).join('\n'));
	await page.keyboard.press('Escape');
	await page.waitForTimeout(100);
	log('  nach Escape: hidden=' + await page.evaluate(() => document.getElementById('fwg-menu').hidden) + ' active=' + await page.evaluate(describeActive));
	// Burger per Tab erreichbar? Reihenfolge Mobile
	await page.goto(base + '/', { waitUntil: 'networkidle' });
	for (let i = 1; i <= 6; i++) { await page.keyboard.press('Tab'); log(`  mobile Tab ${i}: ` + await page.evaluate(describeActive)); }
	log('  aria-snapshot Header:\n' + (await page.locator('header.fwg-header').ariaSnapshot()).split('\n').map((l) => '    ' + l).join('\n'));
	await page.close();
	await ctx.close();
}

/* ---- 4) Exit-Popup (Desktop, Hover-Gerät) ---- */
{
	const ctx = await browser.newContext({ viewport: { width: 1280, height: 800 }, locale: 'de-DE' });
	const page = await ctx.newPage();
	await page.goto(base + '/golfplaetze/', { waitUntil: 'networkidle' });
	log('\nPOPUP desktop /golfplaetze/');
	log('  hover-media=' + await page.evaluate(() => matchMedia('(hover: hover) and (pointer: fine)').matches));
	await page.focus('#fwg-anlage');
	await page.keyboard.type('Test');
	await page.waitForTimeout(8300);
	await page.evaluate(() => document.dispatchEvent(new MouseEvent('mouseout', { clientY: -1, relatedTarget: null, bubbles: true })));
	await page.waitForTimeout(200);
	log('  nach mouseout: popupHidden=' + await page.evaluate(() => document.querySelector('[data-fwg-popup]').hidden) + ' active=' + await page.evaluate(describeActive));
	log('  aria-snapshot Popup:\n' + (await page.locator('[data-fwg-popup]').ariaSnapshot()).split('\n').map((l) => '    ' + l).join('\n'));
	await page.keyboard.press('Escape');
	await page.waitForTimeout(100);
	log('  nach Escape: popupHidden=' + await page.evaluate(() => document.querySelector('[data-fwg-popup]').hidden) + ' active=' + await page.evaluate(describeActive));
	await page.keyboard.press('Tab');
	log('  Tab danach: ' + await page.evaluate(describeActive));
	await page.close();
	await ctx.close();
}

/* ---- 5) Formular-Fehlerpfad mit abgefangenem Netzwerk (kein Request an den Server) ---- */
const fieldState = () => [...document.querySelectorAll('form[data-fwg-form] .fwg-field, form[data-fwg-form] fieldset')].map((f) => {
	const inp = f.querySelector('input:not([type=hidden]), textarea, select');
	const err = f.querySelector('[data-fwg-error]');
	const hid = f.querySelector('input[type=hidden]');
	return `${(inp && (inp.id || inp.name)) || 'fieldset'}: invalid=${inp && inp.getAttribute('aria-invalid')} describedby=${inp && inp.getAttribute('aria-describedby')} err=${err ? (err.hidden ? 'hidden' : '"' + err.textContent.slice(0, 30) + '" id=' + err.id) : '-'} class=${f.classList.contains('is-invalid') ? 'is-invalid' : '-'}${hid ? ' hiddenInput[invalid=' + hid.getAttribute('aria-invalid') + ',describedby=' + hid.getAttribute('aria-describedby') + ']' : ''}`;
});
for (const t of [{ p: '/voranmeldung/', errors: { vorname: 'Sag uns bitte deinen Vornamen.', email: 'Diese E-Mail-Adresse sieht nicht vollständig aus.', wunschplaetze: 'Nenn uns mindestens einen Platz, den du spielen willst.', datenschutz: 'Bitte bestätige den Hinweis zum Datenschutz.' } }, { p: '/unterstuetzen/', errors: { name: 'Sag uns bitte deinen Namen.', email: 'Diese E-Mail-Adresse sieht nicht vollständig aus.', rolle: 'Wähle aus, wie du unterstützen möchtest.', datenschutz: 'Bitte bestätige den Hinweis zum Datenschutz.' } }]) {
	const ctx = await browser.newContext({ viewport: { width: 1280, height: 800 }, locale: 'de-DE' });
	const page = await ctx.newPage();
	let intercepted = 0;
	await page.route('**/admin-ajax.php*', (route) => { intercepted++; route.fulfill({ json: { ok: false, message: 'Bitte schau dir die markierten Felder noch einmal an.', errors: t.errors } }); });
	await page.goto(base + t.p, { waitUntil: 'networkidle' });
	log(`\nFORM-FEHLER (gemockt) ${t.p}`);
	log('  vorher: ' + await page.evaluate(() => { const m = document.querySelector('[data-fwg-msg]'); return `msg hidden=${m.hidden} role=${m.getAttribute('role')} live=${m.getAttribute('aria-live')}`; }));
	await page.focus('[data-fwg-submit]');
	await page.keyboard.press('Enter');
	await page.waitForTimeout(500);
	log('  abgefangen=' + intercepted + ' active=' + await page.evaluate(describeActive));
	log('  msg: ' + await page.evaluate(() => { const m = document.querySelector('[data-fwg-msg]'); return `hidden=${m.hidden} text="${m.textContent}" class=${m.className}`; }));
	for (const l of await page.evaluate(fieldState)) log('  ' + l);
	await page.close();
	await ctx.close();
}

/* ---- 6) Chips, Preis-Toggle, FAQ ---- */
{
	const ctx = await browser.newContext({ viewport: { width: 1280, height: 800 }, locale: 'de-DE' });
	const page = await ctx.newPage();
	await page.goto(base + '/voranmeldung/', { waitUntil: 'networkidle' });
	log('\nCHIPS /voranmeldung/');
	await page.focus('#fwg-wunsch');
	await page.keyboard.type('GC Valley');
	await page.keyboard.press('Enter');
	await page.keyboard.type('GC Eichenried,');
	await page.waitForTimeout(100);
	log('  liste: ' + await page.evaluate(() => { const l = document.querySelector('[data-fwg-chips-list]'); return `n=${l.children.length} live=${l.getAttribute('aria-live')} relevant=${l.getAttribute('aria-relevant')} label=${l.getAttribute('aria-label')} buttons=${[...l.querySelectorAll('button')].map((b) => b.getAttribute('aria-label') + ' ' + Math.round(b.getBoundingClientRect().width) + 'x' + Math.round(b.getBoundingClientRect().height)).join('; ')} hiddenValue="${document.querySelector('input[type=hidden][name=wunschplaetze]').value}" inputName="${document.getElementById('fwg-wunsch').name}"`; }));
	await page.keyboard.press('Shift+Tab');
	log('  Shift+Tab vom Input: ' + await page.evaluate(describeActive));
	await page.keyboard.press('Enter');
	await page.waitForTimeout(100);
	log('  Enter auf Entfernen: n=' + await page.evaluate(() => document.querySelector('[data-fwg-chips-list]').children.length) + ' active=' + await page.evaluate(describeActive));
	await page.keyboard.press('Backspace');
	log('  Backspace bei leerem Input: n=' + await page.evaluate(() => document.querySelector('[data-fwg-chips-list]').children.length));
	await page.keyboard.press('Tab');
	log('  Tab vom Input: ' + await page.evaluate(describeActive));
	await page.goto(base + '/', { waitUntil: 'networkidle' });
	log('\nTOGGLE+FAQ /');
	await page.focus('[data-fwg-preis="jahr"]');
	await page.keyboard.press('Enter');
	log('  toggle: ' + await page.evaluate(() => [...document.querySelectorAll('[data-fwg-preis]')].map((b) => b.textContent + '=' + b.getAttribute('aria-pressed')).join(' ') + ' sichtbar=' + [...document.querySelectorAll('[data-fwg-preis-jahr]')].filter((e) => !e.hidden).length + '/5 jahr'));
	log('  faq: ' + await page.evaluate(() => [...document.querySelectorAll('.fwg-faq details')].map((d) => (d.open ? 'open' : 'closed')).join(',')));
	await page.focus('.fwg-faq details:nth-child(2) summary');
	await page.keyboard.press('Enter');
	await page.waitForTimeout(300);
	log('  nach Enter auf 2. summary: ' + await page.evaluate(() => [...document.querySelectorAll('.fwg-faq details')].map((d) => (d.open ? 'open' : 'closed')).join(',')));
	log('  aria-snapshot FAQ (Auszug):\n' + (await page.locator('.fwg-faq').ariaSnapshot()).split('\n').slice(0, 8).map((l) => '    ' + l).join('\n'));
	log('  aria-snapshot Toggle:\n' + (await page.locator('.fwg-toggle').ariaSnapshot()).split('\n').map((l) => '    ' + l).join('\n'));
	await page.close();
	await ctx.close();
}

/* ---- 7) Zielgrößen ---- */
const targets = () => [...document.querySelectorAll('a[href], button, input:not([type=hidden]), select, textarea, summary')].filter((el) => !el.closest('[hidden]') && !el.closest('.fwg-hp')).map((el) => { const r = el.getBoundingClientRect(); return { t: el.tagName.toLowerCase() + '.' + String(el.className).split(' ')[0], n: (el.getAttribute('aria-label') || el.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 25), w: Math.round(r.width), h: Math.round(r.height), inline: getComputedStyle(el).display === 'inline' }; }).filter((x) => x.w > 0 && x.h > 0);
for (const vp of [{ n: 'desktop', w: 1280, h: 800 }, { n: 'mobile', w: 360, h: 780, mobile: true }]) {
	const ctx = await browser.newContext({ viewport: { width: vp.w, height: vp.h }, isMobile: !!vp.mobile, hasTouch: !!vp.mobile, locale: 'de-DE' });
	log(`\nZIELGRÖSSEN ${vp.n}`);
	for (const p of ['/', '/voranmeldung/', '/golfplaetze/', '/unterstuetzen/', '/ueber-uns/']) {
		const page = await ctx.newPage();
		await page.goto(base + p, { waitUntil: 'networkidle' });
		await reveal(page);
		if (vp.mobile) { await page.evaluate(() => { document.getElementById('fwg-menu').hidden = false; }); }
		const list = await page.evaluate(targets);
		const small = list.filter((x) => x.w < 24 || x.h < 24);
		const touch = list.filter((x) => (x.w < 44 || x.h < 44) && !x.inline);
		log(`  ${p}: ${list.length} Ziele; <24px: ${JSON.stringify(small)}; <44px (Block): ${touch.map((x) => `${x.t}"${x.n}"${x.w}x${x.h}`).join(', ') || '-'}`);
		await page.close();
	}
	await ctx.close();
}

/* ---- 8) Reflow 320 px, Textabstand, reduced motion ---- */
{
	const ctx = await browser.newContext({ viewport: { width: 320, height: 640 }, isMobile: true, hasTouch: true, locale: 'de-DE' });
	log('\nREFLOW 320px');
	for (const p of pages) {
		const page = await ctx.newPage();
		await page.goto(base + p, { waitUntil: 'networkidle' });
		await reveal(page);
		const m = await page.evaluate(() => {
			const over = [...document.querySelectorAll('body *')].filter((el) => { const b = el.getBoundingClientRect(); const cs = getComputedStyle(el); return b.width > 0 && cs.position !== 'fixed' && !el.closest('.fwg-modelle') && (b.right > innerWidth + 1 || b.left < -1); }).slice(0, 8).map((el) => el.tagName.toLowerCase() + '.' + String(el.className).split(' ')[0] + ' l=' + Math.round(el.getBoundingClientRect().left) + ' r=' + Math.round(el.getBoundingClientRect().right));
			const mod = document.querySelector('.fwg-modelle');
			return { sw: document.scrollingElement.scrollWidth, iw: innerWidth, over, carousel: mod ? `${mod.scrollWidth}/${mod.clientWidth} overflowX=${getComputedStyle(mod).overflowX}` : '-' };
		});
		log(`  ${p}: scrollW ${m.sw}/${m.iw} over=${JSON.stringify(m.over)} modelle=${m.carousel}`);
		if (p === '/') {
			await page.screenshot({ path: '/w/screenshots/a11y-320-start.png', fullPage: true });
			await page.evaluate(() => { document.getElementById('fwg-menu').hidden = false; });
			const mm = await page.evaluate(() => { const m = document.getElementById('fwg-menu'); return `scroll ${m.scrollWidth}/${m.clientWidth}, höhe ${m.scrollHeight}/${m.clientHeight}`; });
			log(`  Menü offen 320: ${mm}`);
			await page.screenshot({ path: '/w/screenshots/a11y-320-menu.png' });
		}
		if (p === '/voranmeldung/') await page.screenshot({ path: '/w/screenshots/a11y-320-voranmeldung.png', fullPage: true });
		await page.close();
	}
	await ctx.close();
	const ctx2 = await browser.newContext({ viewport: { width: 1280, height: 800 }, locale: 'de-DE' });
	log('\nTEXTABSTAND (1.4.12) desktop');
	for (const p of ['/', '/voranmeldung/', '/golfplaetze/']) {
		const page = await ctx2.newPage();
		await page.goto(base + p, { waitUntil: 'networkidle' });
		await reveal(page);
		await page.addStyleTag({ content: '* { line-height: 1.5 !important; letter-spacing: 0.12em !important; word-spacing: 0.16em !important; } p { margin-bottom: 2em !important; }' });
		await page.waitForTimeout(200);
		const r = await page.evaluate(() => {
			const clipped = [...document.querySelectorAll('body *')].filter((el) => { const cs = getComputedStyle(el); return (cs.overflowX !== 'visible' || cs.overflowY !== 'visible') && !el.closest('.fwg-modelle') && !el.matches('.fwg-modelle, textarea, select, input, html, body') && (el.scrollWidth > el.clientWidth + 2 || el.scrollHeight > el.clientHeight + 2); }).slice(0, 8).map((el) => el.tagName.toLowerCase() + '.' + String(el.className).split(' ')[0] + ` ${el.scrollWidth}/${el.clientWidth} ${el.scrollHeight}/${el.clientHeight}`);
			const hdr = document.querySelector('.fwg-header__inner');
			const kids = [...hdr.children].map((k) => k.tagName.toLowerCase() + '.' + String(k.className).split(' ')[0] + ' ' + Math.round(k.getBoundingClientRect().left) + '-' + Math.round(k.getBoundingClientRect().right));
			return { clipped, hdrH: hdr.getBoundingClientRect().height, kids, sw: document.scrollingElement.scrollWidth };
		});
		log(`  ${p}: scrollW=${r.sw} headerH=${r.hdrH} header=${r.kids.join(' | ')} clipped=${JSON.stringify(r.clipped)}`);
		if (p === '/') await page.screenshot({ path: '/w/screenshots/a11y-textspacing-start.png', fullPage: false });
		await page.close();
	}
	await ctx2.close();
}
for (const rm of ['no-preference', 'reduce']) {
	const ctx = await browser.newContext({ viewport: { width: 1280, height: 800 }, reducedMotion: rm, locale: 'de-DE' });
	const page = await ctx.newPage();
	await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
	const r = await page.evaluate(() => ({ heroAnim: getComputedStyle(document.querySelector('.fwg-hero__title')).animationName, revealOpacity: getComputedStyle(document.querySelector('[data-fwg-reveal]')).opacity, scroll: getComputedStyle(document.documentElement).scrollBehavior, faqIcon: getComputedStyle(document.querySelector('.fwg-faq__icon')).transitionDuration }));
	log(`\nMOTION ${rm}: ${JSON.stringify(r)}`);
	await page.close();
	await ctx.close();
}

await browser.close();
log('\nFERTIG');
