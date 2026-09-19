// Viewport-Screenshots (nicht Ganzseite), z. B. für Consent-Banner: node viewport-shot.mjs <url> <name>
import { chromium } from 'playwright';
const [url, name] = process.argv.slice(2);
const browser = await chromium.launch();
for (const [w, h, tag] of [[1440, 900, 'desktop'], [390, 800, 'mobile']]) {
  const page = await browser.newPage({ viewport: { width: w, height: h } });
  await page.goto(url, { waitUntil: 'networkidle' });
  await page.waitForTimeout(800);
  await page.screenshot({ path: `screenshots/${name}-${tag}-viewport.png` });
  await page.close();
}
await browser.close();
