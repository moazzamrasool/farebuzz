const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');

const TRIP_FILE = process.argv[2] || 'nepal-muktinath';

(async () => {
  const htmlPath = path.join(__dirname, 'output', `${TRIP_FILE}.html`);
  const outDir = path.join(__dirname, 'output', 'previews');
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

  const browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: 'new',
    args: ['--no-sandbox', '--disable-gpu'],
    protocolTimeout: 180000,
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 720, deviceScaleFactor: 1.5 });
  await page.goto('file:///' + htmlPath.replace(/\\/g, '/'), { waitUntil: 'load', timeout: 60000 });
  await page.evaluateHandle('document.fonts.ready');

  const count = await page.$$eval('.page', els => els.length);
  console.log('pages:', count);
  for (let i = 0; i < count; i++) {
    const el = (await page.$$('.page'))[i];
    await el.screenshot({ path: path.join(outDir, `page-${String(i + 1).padStart(2, '0')}.png`) });
  }
  await browser.close();
  console.log('done');
})().catch(e => { console.error(e); process.exit(1); });
