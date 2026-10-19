import puppeteer from 'puppeteer-core';
// Tracking checks (spec A6): consent gate, dataLayer events, UTM capture.
// Needs a gtm_id in config.local.php (any value, e.g. GTM-TEST123) and an assessment row with token 'a'×32.
// Run: npm i puppeteer-core && node tests/tracking.mjs [base]
const B = (process.argv[2] || 'http://127.0.0.1:8080') + '/arabia';
const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', headless: 'new' });
const results = [];
const ok = (cond, msg) => results.push((cond ? 'PASS ' : 'FAIL ') + msg);
const page = await browser.newPage();
const gtmReqs = [];
await page.setRequestInterception(true);
page.on('request', r => { if (r.url().includes('googletagmanager.com')) { gtmReqs.push(r.url()); r.abort(); } else r.continue(); });

// 1. Consent gate
await page.goto(B + '/?utm_source=google&utm_campaign=b1-ksa&gclid=abc123', { waitUntil: 'networkidle0' });
ok(gtmReqs.length === 0, 'GTM not requested before consent');
ok(await page.$eval('[data-cookie-banner]', el => getComputedStyle(el).display !== 'none'), 'cookie banner visible on first visit');
await page.click('[data-cookie="accept"]');
await new Promise(r => setTimeout(r, 500));
ok(gtmReqs.some(u => u.includes('gtm.js?id=GTM-TEST123')), 'GTM loads after Accept');
ok(await page.$eval('[data-cookie-banner]', el => getComputedStyle(el).display === 'none' || el.hidden), 'banner hidden after Accept');

// 2. Reject path on a fresh profile
const ctx = await browser.createBrowserContext();
const p2 = await ctx.newPage(); const g2 = [];
await p2.setRequestInterception(true);
p2.on('request', r => { if (r.url().includes('googletagmanager.com')) { g2.push(r.url()); r.abort(); } else r.continue(); });
await p2.goto(B + '/ar/', { waitUntil: 'networkidle0' });
await p2.click('[data-cookie="reject"]');
await p2.goto(B + '/ar/contact/', { waitUntil: 'networkidle0' });
ok(g2.length === 0, 'GTM never loads after Reject');
await ctx.close();

// 3. UTM / click-ID capture into hidden fields (same session as step 1)
await page.goto(B + '/contact/', { waitUntil: 'networkidle0' });
const hidden = await page.$eval('form[data-lead-form]', f => Object.fromEntries(['utm_source', 'utm_campaign', 'gclid', 'landing_page', 'page_language'].map(n => [n, f.querySelector(`[name="${n}"]`).value])));
ok(hidden.utm_source === 'google' && hidden.utm_campaign === 'b1-ksa' && hidden.gclid === 'abc123', 'UTM + gclid carried to form: ' + JSON.stringify(hidden));
ok(hidden.landing_page.includes('utm_source=google') && hidden.page_language === 'en', 'landing_page + page_language set');

// 4. Click events
await page.evaluate(() => document.addEventListener('click', e => e.preventDefault(), true));
for (const [sel, ev] of [['a[href^="tel:"]', 'phone_click'], ['.wa-float', 'whatsapp_click']]) {
  let el = null; // first visible match (the header phone links sit in closed menus)
  for (const h of await page.$$(sel)) if (await h.boundingBox()) { el = h; break; }
  if (el) { await el.click(); const has = await page.evaluate(ev => dataLayer.some(d => d.event === ev && d.page_language && d.page_path), ev); ok(has, ev + ' pushed with page_language/page_path'); } else ok(false, 'no element for ' + sel);
}
const hasMail = await page.evaluate(() => { const a = document.createElement('a'); a.href = 'mailto:x@y.z'; a.textContent = 'm'; document.body.appendChild(a); a.click(); return dataLayer.some(d => d.event === 'email_click'); });
ok(hasMail, 'email_click pushed for mailto links');

// 5. Conversion events
await page.goto(B + '/thank-you/?f=demo&i=sap', { waitUntil: 'networkidle0' });
const evs = await page.evaluate(() => dataLayer.filter(d => d.event).map(d => d.event + ':' + d.page_language));
ok(evs.includes('generate_lead:en') && evs.includes('book_demo:en'), 'thank-you (demo) fires generate_lead + book_demo: ' + evs.join(','));
await page.goto(B + '/ar/thank-you/?f=lead&i=zatca', { waitUntil: 'networkidle0' });
const evs2 = await page.evaluate(() => dataLayer.filter(d => d.event).map(d => d.event + ':' + d.page_language));
ok(evs2.includes('generate_lead:ar') && !evs2.includes('book_demo:ar'), 'AR thank-you (lead) fires generate_lead only');
await page.goto(B + '/sap-business-one/free-assessment/result/?t=' + 'a'.repeat(32) + '&new=1', { waitUntil: 'networkidle0' });
const evs3 = await page.evaluate(() => dataLayer.filter(d => d.event).map(d => d.event));
ok(evs3.includes('assessment_complete'), 'assessment result fires assessment_complete');

console.log(results.join('\n'));
process.exitCode = results.some(r => r.startsWith('FAIL')) ? 1 : 0;
await browser.close();
