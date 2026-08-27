const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const srcDir = path.join(__dirname, 'assets', 'images');
const outDir = path.join(__dirname, 'assets', 'images-optimized');

if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

(async () => {
  const files = fs.readdirSync(srcDir).filter(f => /\.(jpg|jpeg|png)$/i.test(f));
  for (const f of files) {
    const inPath = path.join(srcDir, f);
    const outPath = path.join(outDir, f.replace(/\.(jpeg|png)$/i, '.jpg'));
    await sharp(inPath, { failOn: 'none' })
      .rotate()
      .resize({ width: 1600, height: 1600, fit: 'inside', withoutEnlargement: true })
      .jpeg({ quality: 78, mozjpeg: true })
      .toFile(outPath);
    const before = fs.statSync(inPath).size;
    const after = fs.statSync(outPath).size;
    console.log(`${f}: ${(before/1024).toFixed(0)}KB -> ${(after/1024).toFixed(0)}KB`);
  }
})();
