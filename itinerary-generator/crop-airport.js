const sharp = require('sharp');
const path = require('path');
const inPath = path.join(__dirname, 'assets', 'images', 'kathmandu_airport.jpg');
const outPath = path.join(__dirname, 'assets', 'images-optimized', 'kathmandu_airport.jpg');

sharp(inPath, { failOn: 'none' })
  .rotate()
  .metadata()
  .then(meta => {
    const cropHeight = Math.round(meta.height * 0.90); // drop bottom ~10% timestamp band
    return sharp(inPath, { failOn: 'none' })
      .rotate()
      .extract({ left: 0, top: 0, width: meta.width, height: cropHeight })
      .resize({ width: 1600, height: 1600, fit: 'inside', withoutEnlargement: true })
      .jpeg({ quality: 78, mozjpeg: true })
      .toFile(outPath);
  })
  .then(info => console.log('OK', info))
  .catch(e => { console.error('ERR', e.message); process.exit(1); });
