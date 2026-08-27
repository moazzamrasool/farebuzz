const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const { buildHTML } = require('./template/render');

const TRIP_FILE = process.argv[2] || 'nepal-muktinath';

function findChrome() {
  const candidates = [
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
  ];
  for (const p of candidates) if (fs.existsSync(p)) return p;
  throw new Error('No local Chrome/Edge install found. Set CHROME_PATH env var.');
}

async function main() {
  const brand = JSON.parse(fs.readFileSync(path.join(__dirname, 'data', 'brand.json'), 'utf8'));
  const trip = JSON.parse(fs.readFileSync(path.join(__dirname, 'data', `${TRIP_FILE}.json`), 'utf8'));
  const assetsDir = path.join(__dirname, 'assets');

  const html = buildHTML(trip, brand, assetsDir);

  const outDir = path.join(__dirname, 'output');
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });
  const htmlPath = path.join(outDir, `${TRIP_FILE}.html`);
  fs.writeFileSync(htmlPath, html, 'utf8');
  console.log('Wrote', htmlPath);

  const executablePath = process.env.CHROME_PATH || findChrome();
  console.log('Using Chrome at', executablePath);
  const browser = await puppeteer.launch({
    executablePath,
    headless: 'new',
    args: ['--no-sandbox', '--disable-gpu'],
    protocolTimeout: 180000,
  });
  console.log('step: browser launched');
  try {
    const page = await browser.newPage();
    console.log('step: page created');
    await page.setViewport({ width: 1280, height: 720, deviceScaleFactor: 2 });
    console.log('step: viewport set');
    await page.goto('file:///' + htmlPath.replace(/\\/g, '/'), { waitUntil: 'load', timeout: 60000 });
    console.log('step: navigated');
    // give web fonts a moment to finish swapping in
    await page.evaluateHandle('document.fonts.ready');
    console.log('step: fonts ready');
    await new Promise(r => setTimeout(r, 500));

    const pdfPath = path.join(outDir, `${TRIP_FILE}.pdf`);
    await page.pdf({
      path: pdfPath,
      width: '1280px',
      height: '720px',
      printBackground: true,
      pageRanges: '',
      margin: { top: 0, right: 0, bottom: 0, left: 0 },
      timeout: 120000,
    });
    console.log('Wrote', pdfPath);
  } finally {
    await browser.close();
  }
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
