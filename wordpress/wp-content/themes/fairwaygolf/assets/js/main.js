/* Fair-Way-Golf: Menü, Preise, Chips, Formulare, Lieblingsplatz-Popup, Reveal. Kein jQuery. */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---- Mobile-Menü mit Fokusfalle ---- */
	function initMenu() {
		var burger = document.querySelector('[data-fwg-menu-open]');
		var menu = document.querySelector('[data-fwg-menu]');
		if (!burger || !menu) { return; }
		var lastFocus = null;
		var focusables = function () {
			return Array.prototype.slice.call(menu.querySelectorAll('a[href], button:not([disabled])'));
		};
		var open = function () {
			lastFocus = document.activeElement;
			menu.hidden = false;
			burger.setAttribute('aria-expanded', 'true');
			document.body.classList.add('fwg-noscroll');
			var first = menu.querySelector('[data-fwg-menu-close]');
			if (first) { first.focus(); }
		};
		var close = function () {
			if (menu.hidden) { return; }
			menu.hidden = true;
			burger.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('fwg-noscroll');
			if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
		};
		burger.addEventListener('click', open);
		menu.querySelectorAll('[data-fwg-menu-close], nav a, .fwg-menu__cta a').forEach(function (el) {
			el.addEventListener('click', close);
		});
		document.addEventListener('keydown', function (e) {
			if (menu.hidden) { return; }
			if (e.key === 'Escape') { close(); return; }
			if (e.key === 'Tab') {
				var list = focusables();
				if (!list.length) { return; }
				var first = list[0], last = list[list.length - 1];
				if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
		window.addEventListener('resize', function () { if (window.innerWidth >= 900) { close(); } });
	}

	/* ---- Preis-Umschalter (monatlich / jährlich) ---- */
	function initPrices() {
		var buttons = document.querySelectorAll('[data-fwg-preis]');
		if (!buttons.length) { return; }
		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var mode = btn.getAttribute('data-fwg-preis');
				buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
				document.querySelectorAll('[data-fwg-preis-monat]').forEach(function (el) { el.hidden = mode !== 'monat'; });
				document.querySelectorAll('[data-fwg-preis-jahr]').forEach(function (el) { el.hidden = mode !== 'jahr'; });
			});
		});
	}

	/* ---- Wunschplätze als Chips ---- */
	function initChips() {
		document.querySelectorAll('[data-fwg-chips]').forEach(function (wrap) {
			var input = wrap.querySelector('input[type="text"]');
			var list = wrap.querySelector('[data-fwg-chips-list]');
			if (!input || !list) { return; }
			var hint = wrap.querySelector('[data-fwg-chips-hint]');
			if (hint) { hint.textContent = hint.getAttribute('data-fwg-chips-hint'); }
			var hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.name = input.name;
			input.name = '';
			input.classList.add('fwg-chips__input');
			wrap.appendChild(hidden);
			var values = [];
			var sync = function () {
				hidden.value = values.join(', ');
				list.innerHTML = '';
				values.forEach(function (v, i) {
					var li = document.createElement('li');
					li.className = 'fwg-chip';
					li.textContent = v;
					var rm = document.createElement('button');
					rm.type = 'button';
					rm.setAttribute('aria-label', v + ' entfernen');
					rm.innerHTML = '<svg class="fwg-icon" viewBox="0 0 256 256" aria-hidden="true"><path fill="currentColor" d="M205.66 194.34a8 8 0 0 1-11.32 11.32L128 139.31l-66.34 66.35a8 8 0 0 1-11.32-11.32L116.69 128 50.34 61.66a8 8 0 0 1 11.32-11.32L128 116.69l66.34-66.35a8 8 0 0 1 11.32 11.32L139.31 128Z"/></svg>';
					rm.addEventListener('click', function () { values.splice(i, 1); sync(); input.focus(); });
					li.appendChild(rm);
					list.appendChild(li);
				});
			};
			var add = function () {
				var raw = input.value.split(/[,;\n]+/);
				raw.forEach(function (v) {
					v = v.trim();
					if (v && values.length < 10 && values.indexOf(v) === -1) { values.push(v.slice(0, 80)); }
				});
				input.value = '';
				sync();
			};
			input.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); add(); }
				if (e.key === 'Backspace' && !input.value && values.length) { values.pop(); sync(); }
			});
			input.addEventListener('blur', add);
			input.addEventListener('input', function () { if (input.value.indexOf(',') !== -1) { add(); } });
			// Vorbelegung aus Nicht-JS-Fallback (kommagetrennt) oder Query-Parameter
			var form = wrap.closest('form');
			if (form) { form.addEventListener('submit', add); }
		});
	}

	/* ---- Formulare per fetch ---- */
	function setError(form, field, msg) {
		var el = form.querySelector('[data-fwg-error="' + field + '"]');
		var input = form.querySelector('[name="' + field + '"]');
		if (el) { el.textContent = msg || ''; el.hidden = !msg; }
		var holder = input ? input.closest('.fwg-field') : (el ? el.closest('.fwg-field') : null);
		if (holder) { holder.classList.toggle('is-invalid', !!msg); }
		if (input && msg) { input.setAttribute('aria-invalid', 'true'); if (el && el.id) { input.setAttribute('aria-describedby', el.id); } }
		if (input && !msg) { input.removeAttribute('aria-invalid'); }
	}

	function initForms() {
		if (!window.fwgAjax) { return; }
		document.querySelectorAll('[data-fwg-form]').forEach(function (form) {
			var type = form.getAttribute('data-fwg-form');
			var msg = form.querySelector('[data-fwg-msg]');
			var submit = form.querySelector('[data-fwg-submit]');
			var done = form.parentElement.querySelector('[data-fwg-done]');
			form.querySelectorAll('[data-fwg-error]').forEach(function (el, i) { if (!el.id) { el.id = 'fwg-err-' + type + '-' + i; } });
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				form.querySelectorAll('[data-fwg-error]').forEach(function (el) { setError(form, el.getAttribute('data-fwg-error'), ''); });
				if (msg) { msg.hidden = true; msg.classList.remove('is-error'); }
				var data = new FormData(form);
				data.set('action', 'fwg_form');
				data.set('nonce', window.fwgAjax.nonce);
				if (submit) { submit.classList.add('is-loading'); submit.setAttribute('aria-busy', 'true'); }
				fetch(window.fwgAjax.url, { method: 'POST', body: data, credentials: 'same-origin' })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						if (res.ok) {
							if (type === 'lieblingsplatz') {
								try { localStorage.setItem('fwgLieblingsplatz', '1'); } catch (err) { /* privat */ }
							}
							if (done) {
								var text = done.querySelector('[data-fwg-done-text]');
								if (text) { text.textContent = res.message; }
								form.hidden = true;
								done.hidden = false;
								if (done.focus) { done.focus(); }
								if (done.scrollIntoView && !reduceMotion) { done.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
							} else if (msg) {
								msg.textContent = res.message; msg.hidden = false;
							}
							form.reset();
						} else {
							var errors = res.errors || {};
							var first = null;
							Object.keys(errors).forEach(function (k) {
								setError(form, k, errors[k]);
								if (!first) { first = form.querySelector('[name="' + k + '"]') || form.querySelector('[data-fwg-error="' + k + '"]'); }
							});
							if (msg) { msg.textContent = res.message || 'Das hat nicht geklappt.'; msg.classList.add('is-error'); msg.hidden = false; }
							if (first && first.focus) { first.focus(); }
						}
					})
					.catch(function () {
						if (msg) { msg.textContent = 'Keine Verbindung. Bitte versuch es gleich noch einmal oder schreib uns per E-Mail.'; msg.classList.add('is-error'); msg.hidden = false; }
					})
					.finally(function () {
						if (submit) { submit.classList.remove('is-loading'); submit.removeAttribute('aria-busy'); }
					});
			});
		});
	}

	/* ---- Exit-Intent: „Sag uns deinen Lieblingsplatz" (nur Desktop, einmal pro Sitzung) ---- */
	function initPopup() {
		var popup = document.querySelector('[data-fwg-popup]');
		if (!popup) { return; }
		var canHover = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
		var seen = false;
		try { seen = sessionStorage.getItem('fwgPopup') === '1' || localStorage.getItem('fwgLieblingsplatz') === '1'; } catch (err) { seen = false; }
		if (!canHover || seen) { return; }
		var armed = false;
		setTimeout(function () { armed = true; }, 8000);
		var closed = false;
		var show = function () {
			if (!armed || closed || !popup.hidden) { return; }
			var a = document.activeElement;
			if (a && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName)) { return; } // nicht mitten ins Tippen platzen
			popup.hidden = false;
			try { sessionStorage.setItem('fwgPopup', '1'); } catch (err) { /* privat */ }
			var input = popup.querySelector('input[name="platz"]');
			if (input) { input.focus({ preventScroll: true }); }
		};
		var hide = function () { popup.hidden = true; closed = true; }; // nach dem Schließen auf dieser Seite Ruhe
		document.addEventListener('mouseout', function (e) {
			if (!e.relatedTarget && e.clientY <= 0) { show(); }
		});
		popup.querySelectorAll('[data-fwg-popup-close]').forEach(function (b) { b.addEventListener('click', hide); });
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !popup.hidden) { hide(); } });
	}

	/* ---- Reveal beim Scrollen ---- */
	function initReveal() {
		var items = document.querySelectorAll('[data-fwg-reveal]');
		if (!items.length) { return; }
		if (reduceMotion || !('IntersectionObserver' in window)) {
			items.forEach(function (el) { el.classList.add('is-in'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });
		items.forEach(function (el) { io.observe(el); });
	}

	/* ---- Cookie-Einstellungen (Klaro) ---- */
	function initConsentLink() {
		document.querySelectorAll('[data-fwg-consent]').forEach(function (a) {
			a.addEventListener('click', function (e) {
				e.preventDefault();
				if (window.klaro && window.klaro.show) { window.klaro.show(undefined, true); }
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initMenu();
		initPrices();
		initChips();
		initForms();
		initPopup();
		initReveal();
		initConsentLink();
	});
})();
