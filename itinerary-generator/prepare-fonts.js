const fs = require('fs');
const path = require('path');
const https = require('https');

const fontsDir = path.join(__dirname, 'assets', 'fonts');
const cssPath = path.join(fontsDir, 'fonts.css');
let css = fs.readFileSync(cssPath, 'utf8');

function fetchBuffer(url) {
  return new Promise((resolve, reject) => {
    https.get(url, res => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        return fetchBuffer(res.headers.location).then(resolve, reject);
      }
      if (res.statusCode !== 200) return reject(new Error(`${res.statusCode} for ${url}`));
      const chunks = [];
      res.on('data', c => chunks.push(c));
      res.on('end', () => resolve(Buffer.concat(chunks)));
    }).on('error', reject);
  });
}

(async () => {
  const urls = [...new Set((css.match(/https:\/\/fonts\.gstatic\.com\/[^)]+/g) || []))];
  console.log(`Found ${urls.length} font files to embed`);
  for (const url of urls) {
    const buf = await fetchBuffer(url);
    const dataUri = `data:font/woff2;base64,${buf.toString('base64')}`;
    css = css.split(url).join(dataUri);
    console.log('embedded', url, `(${(buf.length/1024).toFixed(0)}KB)`);
  }
  fs.writeFileSync(path.join(fontsDir, 'fonts-inline.css'), css, 'utf8');
  console.log('Wrote fonts-inline.css', (css.length/1024).toFixed(0), 'KB');
})().catch(e => { console.error(e); process.exit(1); });
