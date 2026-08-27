/**
 * Reusable FareBuzzer itinerary brochure renderer.
 * buildHTML(trip, brand) -> full standalone HTML string (one .page div per slide).
 * Swap the `trip` JSON (see data/nepal-muktinath.json) to reuse for any other itinerary.
 */

const fs = require('fs');
const path = require('path');

function esc(s) {
  if (s === null || s === undefined) return '';
  return String(s);
}

function toDataUri(absPath) {
  const ext = path.extname(absPath).slice(1).toLowerCase();
  const mime = ext === 'png' ? 'image/png' : 'image/jpeg';
  const buf = fs.readFileSync(absPath);
  return `data:${mime};base64,${buf.toString('base64')}`;
}

function fontFaceCSS() {
  return fs.readFileSync(path.join(__dirname, '..', 'assets', 'fonts', 'fonts-inline.css'), 'utf8');
}

function css(brand) {
  const c = brand.colors;
  return `
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      color: ${c.ink};
      background: #ccc;
    }
    .page {
      width: 1280px;
      height: 720px;
      position: relative;
      overflow: hidden;
      background: ${c.cream};
      page-break-after: always;
    }
    .page:last-child { page-break-after: auto; }

    /* ---- shared chrome: logo corner + footer on every inner page ---- */
    .brand-logo {
      position: absolute;
      top: 28px;
      left: 40px;
      height: 40px;
      z-index: 5;
    }
    .brand-logo img { height: 100%; display: block; }

    .page-footer {
      position: absolute;
      left: 40px;
      right: 40px;
      bottom: 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 11px;
      color: ${c.navyLight};
      letter-spacing: .04em;
    }
    .page-footer .line {
      position: absolute;
      left: 0; right: 0; top: -10px;
      height: 1px;
      background: ${c.divider};
    }
    .page-footer b { color: ${c.orange}; }

    .page-header {
      padding: 96px 64px 0 64px;
    }
    .kicker {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: .18em;
      color: ${c.orange};
      text-transform: uppercase;
      margin: 0 0 8px 0;
    }
    .page-title {
      font-family: 'Poppins', sans-serif;
      font-weight: 800;
      font-size: 38px;
      color: ${c.navy};
      margin: 0 0 14px 0;
    }
    .title-rule {
      width: 64px;
      height: 5px;
      border-radius: 3px;
      background: ${c.orange};
      margin-bottom: 28px;
    }

    img { object-fit: cover; }
    .photo {
      border-radius: 14px;
      box-shadow: 0 10px 24px rgba(11,37,69,.18);
      display: block;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff;
      border: 1.5px solid ${c.orange};
      color: ${c.navy};
      font-family: 'Inter', sans-serif;
      font-weight: 600;
      font-size: 13.5px;
      padding: 7px 14px;
      border-radius: 999px;
    }
    .pill .dot { width: 6px; height: 6px; border-radius: 50%; background: ${c.orange}; flex: none; }

    .bullets { list-style: none; margin: 0; padding: 0; }
    .bullets li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 17px;
      font-weight: 500;
      line-height: 1.4;
      color: ${c.ink};
      margin-bottom: 13px;
    }
    .bullets li .mark {
      flex: none;
      width: 20px; height: 20px;
      border-radius: 50%;
      background: ${c.navy};
      color: #fff;
      font-size: 12px;
      display: flex; align-items: center; justify-content: center;
      margin-top: 2px;
    }

    .callout {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      background: #FFF4EC;
      border-left: 5px solid ${c.orange};
      border-radius: 8px;
      padding: 14px 18px;
      font-size: 14.5px;
      font-weight: 600;
      color: #7A3D0E;
      max-width: 620px;
    }
    .callout .bang {
      flex: none;
      width: 22px; height: 22px;
      border-radius: 50%;
      background: ${c.orange};
      color: #fff;
      font-family: 'Poppins',sans-serif;
      font-weight: 800;
      font-size: 14px;
      display: flex; align-items: center; justify-content: center;
    }

    .card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 22px rgba(11,37,69,.08);
      border: 1px solid ${c.divider};
    }

    table.tbl { border-collapse: collapse; width: 100%; }
    table.tbl th {
      background: ${c.navy};
      color: #fff;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 13px;
      letter-spacing: .04em;
      text-transform: uppercase;
      padding: 12px 16px;
      text-align: left;
    }
    table.tbl td {
      font-size: 15px;
      font-weight: 500;
      padding: 12px 16px;
      border-bottom: 1px solid ${c.divider};
      color: ${c.ink};
    }
    table.tbl tr:last-child td { border-bottom: none; }
    table.tbl tr:nth-child(even) td { background: #FAFAF8; }
  `;
}

function pageHeader(kicker, title) {
  return `
    <div class="page-header">
      <p class="kicker">${esc(kicker)}</p>
      <h1 class="page-title">${esc(title)}</h1>
      <div class="title-rule"></div>
    </div>
  `;
}

function pageFooter(brand, trip, pageNum) {
  return `
    <div class="page-footer">
      <div class="line"></div>
      <span><b>${esc(brand.wordmark)}</b> &nbsp;·&nbsp; ${esc(trip.title)} ${esc(trip.subtitle)}</span>
      <span>${esc(brand.website)} &nbsp;·&nbsp; ${String(pageNum).padStart(2, '0')}</span>
    </div>
  `;
}

function logoCorner(logoSrc) {
  return `<div class="brand-logo"><img src="${logoSrc}" alt="logo" /></div>`;
}

function img(imgMap, key, style = '') {
  const src = imgMap[key];
  if (!src) return `<div class="photo" style="${style};background:#ddd"></div>`;
  return `<img class="photo" src="${src}" style="${style}" />`;
}

function buildCover(brand, trip, imgMap) {
  const [i1, i2, i3] = trip.coverImages;
  return `
  <section class="page" style="background:${brand.colors.cream};">
    ${logoCorner(imgMap.__logo)}
    <div style="position:absolute; top:28px; right:40px; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; letter-spacing:.16em; color:${brand.colors.navy};">TRAVEL PROPOSAL 2026</div>

    <div style="padding:120px 70px 0 70px;">
      <div style="display:flex; align-items:baseline; gap:22px; flex-wrap:wrap;">
        <h1 style="font-family:'Poppins',sans-serif; font-weight:800; font-size:96px; color:${brand.colors.navy}; margin:0; line-height:.95;">${esc(trip.title)}</h1>
      </div>
      <p style="font-family:'Playfair Display',serif; font-style:italic; font-weight:600; font-size:36px; color:${brand.colors.orange}; margin:6px 0 0 4px;">${esc(trip.subtitle)}</p>
      <p style="font-family:'Poppins',sans-serif; font-weight:700; font-size:19px; letter-spacing:.05em; color:${brand.colors.navy}; margin:22px 0 0 4px;">${esc(trip.tripMonth)}</p>
    </div>

    <div style="position:absolute; left:70px; right:70px; bottom:64px; display:flex; gap:20px;">
      ${img(imgMap, i1, 'width:33.33%;height:260px;')}
      ${img(imgMap, i2, 'width:33.33%;height:260px;')}
      ${img(imgMap, i3, 'width:33.33%;height:260px;')}
    </div>
    <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:${brand.colors.orange};"></div>
  </section>`;
}

function buildOverview(brand, trip, imgMap, pageNum) {
  const c = brand.colors;
  const hotelCards = trip.hotels.map(h => `
    <div class="card" style="padding:16px 18px; flex:1;">
      <div style="font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:${c.orange}; margin-bottom:4px;">${esc(h.city)} &middot; ${esc(h.category)}</div>
      <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:${c.navy}; margin-bottom:6px;">${esc(h.name)}</div>
      <div style="font-size:13px; color:#5b6472; font-weight:500;">${esc(h.detail)}</div>
    </div>
  `).join('');

  return `
  <section class="page">
    ${logoCorner(imgMap.__logo)}
    ${pageHeader('Travel Proposal 2026', 'Trip Overview')}
    <div style="display:flex; gap:40px; padding:0 64px;">
      <div style="flex:0 0 380px;">
        ${img(imgMap, 'muktinath_temple', 'width:380px;height:250px;')}
        <div style="display:flex; gap:14px; margin-top:20px;">
          <div style="flex:1;">
            <div style="font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:${c.orange};">01</div>
            <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; color:${c.navy}; margin:4px 0 8px;">Destinations</div>
            <div style="font-size:14px; color:${c.ink}; font-weight:600;">${trip.destinations.join(' &middot; ')}</div>
          </div>
          <div style="flex:1;">
            <div style="font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:${c.orange};">02</div>
            <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; color:${c.navy}; margin:4px 0 8px;">Duration</div>
            <div style="font-size:14px; color:${c.ink}; font-weight:600;">${esc(trip.duration)}</div>
          </div>
        </div>
      </div>
      <div style="flex:1;">
        <div style="font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:${c.orange};">03</div>
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:${c.navy}; margin:4px 0 14px;">Hotels &middot; 3 Star</div>
        <div style="display:flex; gap:16px;">${hotelCards}</div>

        <div style="margin-top:18px;">
          ${img(imgMap, 'boudhanath', 'width:100%;height:150px;')}
        </div>
      </div>
    </div>
    ${pageFooter(brand, trip, pageNum)}
  </section>`;
}

function buildCostPage(brand, trip, imgMap, pageNum) {
  const c = brand.colors;
  const vehRows = trip.vehicles.rows.map(r => `
    <tr><td><b>${esc(r.pax)}</b></td><td>${esc(r.ktmPokhara)}</td><td>${esc(r.seater)}</td><td>${esc(r.jomsom)}</td></tr>
  `).join('');
  const costRows = trip.packageCost.rows.map(r => `
    <tr><td><b>${esc(r.pax)}</b></td><td style="color:${c.orange}; font-weight:700;">${esc(r.price)} <span style="color:#7a8291; font-weight:500; font-size:12px;">per person</span></td></tr>
  `).join('');

  return `
  <section class="page">
    ${logoCorner(imgMap.__logo)}
    ${pageHeader('Travel Proposal 2026', 'Vehicle & Package Cost')}
    <div style="padding:0 64px; display:flex; gap:36px;">
      <div style="flex:1;">
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:${c.navy}; margin-bottom:10px;">Vehicle by Group Size</div>
        <div class="card" style="overflow:hidden;">
          <table class="tbl">
            <thead><tr><th>Pax</th><th>KTM&ndash;Pokhara</th><th>Seater</th><th>Jomsom</th></tr></thead>
            <tbody>${vehRows}</tbody>
          </table>
        </div>
        <div class="callout" style="margin-top:16px;"><span class="bang">!</span><span>${esc(trip.vehicles.note)}</span></div>

        <div style="margin-top:16px;">
          ${img(imgMap, 'himalaya_range', 'width:100%;height:120px;')}
        </div>
      </div>
      <div style="flex:0 0 420px;">
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:${c.navy}; margin-bottom:10px;">Package Cost per Person</div>
        <div class="card" style="overflow:hidden;">
          <table class="tbl">
            <thead><tr><th>Pax</th><th>Price</th></tr></thead>
            <tbody>${costRows}</tbody>
          </table>
        </div>
        <div class="callout" style="margin-top:16px;"><span class="bang">!</span><span>${esc(trip.packageCost.note)}</span></div>
      </div>
    </div>
    ${pageFooter(brand, trip, pageNum)}
  </section>`;
}

function buildDayPage(brand, trip, day, imgMap, pageNum) {
  const c = brand.colors;
  const chips = day.highlights.map(h => `<span class="pill"><span class="dot"></span>${esc(h)}</span>`).join('');
  const bullets = day.bullets.map(b => `<li><span class="mark">&#10003;</span><span>${esc(b)}</span></li>`).join('');
  const images = day.images || [];

  let imageBlock = '';
  if (images.length === 2) {
    imageBlock = `
      <div style="display:flex; flex-direction:column; gap:14px; flex:0 0 380px;">
        ${img(imgMap, images[0], 'width:380px;height:183px;')}
        ${img(imgMap, images[1], 'width:380px;height:183px;')}
      </div>`;
  } else {
    imageBlock = `
      <div style="flex:0 0 380px;">
        ${img(imgMap, images[0], 'width:380px;height:380px;')}
      </div>`;
  }

  const closing = day.closingLine ? `
    <div style="margin-top:26px; max-width:640px; border-left:4px solid ${c.orange}; padding-left:18px;">
      <span style="font-family:'Playfair Display',serif; font-style:italic; font-size:19px; color:${c.navy}; line-height:1.5;">${esc(day.closingLine)}</span>
    </div>` : '';

  const overnightTag = day.overnight ? `
    <span style="display:inline-block; margin-top:14px; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; letter-spacing:.05em; color:#fff; background:${c.navy}; padding:6px 14px; border-radius:999px;">${esc(day.overnight)}</span>` : '';

  return `
  <section class="page">
    ${logoCorner(imgMap.__logo)}
    <div style="display:flex; align-items:flex-end; gap:22px; padding:70px 64px 0 64px;">
      <div style="font-family:'Playfair Display',serif; font-weight:700; font-size:104px; color:${c.orange}; line-height:.8;">${String(day.day).padStart(2,'0')}</div>
      <div style="padding-bottom:10px;">
        <p class="kicker" style="margin-bottom:4px;">DAY ${day.day}</p>
        <h1 style="font-family:'Poppins',sans-serif; font-weight:800; font-size:32px; color:${c.navy}; margin:0;">${esc(day.city)}</h1>
        ${overnightTag}
      </div>
    </div>

    <div style="display:flex; gap:12px; flex-wrap:wrap; padding:22px 64px 0 64px;">${chips}</div>

    <div style="display:flex; gap:36px; padding:20px 64px 0 64px; align-items:flex-start;">
      ${imageBlock}
      <div style="flex:1;">
        <ul class="bullets">${bullets}</ul>
        ${day.callout ? `<div class="callout"><span class="bang">!</span><span>${esc(day.callout)}</span></div>` : ''}
        ${closing}
      </div>
    </div>
    ${pageFooter(brand, trip, pageNum)}
  </section>`;
}

function buildInclusionsPage(brand, trip, imgMap, pageNum) {
  const c = brand.colors;
  const listBlock = (items, mark, color) => items.map(i => `
    <li style="display:flex; gap:10px; align-items:flex-start; font-size:14.5px; font-weight:500; color:${c.ink}; margin-bottom:11px;">
      <span style="flex:none; width:18px; height:18px; border-radius:50%; background:${color}; color:#fff; font-size:11px; display:flex; align-items:center; justify-content:center; margin-top:2px;">${mark}</span>
      <span>${esc(i)}</span>
    </li>`).join('');

  return `
  <section class="page">
    ${logoCorner(imgMap.__logo)}
    ${pageHeader('Travel Proposal 2026', 'Inclusions, Exclusions & Notes')}
    <div style="display:flex; gap:24px; padding:0 64px;">
      <div class="card" style="flex:1; padding:22px 24px; border-top:5px solid #1E8E3E;">
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:#1E8E3E; margin-bottom:14px;">Inclusions</div>
        <ul style="list-style:none; margin:0; padding:0;">${listBlock(trip.inclusions, '&#10003;', '#1E8E3E')}</ul>
      </div>
      <div class="card" style="flex:1; padding:22px 24px; border-top:5px solid #D93025;">
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:#D93025; margin-bottom:14px;">Exclusions</div>
        <ul style="list-style:none; margin:0; padding:0;">${listBlock(trip.exclusions, '&#10005;', '#D93025')}</ul>
      </div>
      <div class="card" style="flex:1; padding:22px 24px; border-top:5px solid ${c.orange};">
        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:${c.orangeDark}; margin-bottom:14px;">Notes</div>
        <ul style="list-style:none; margin:0; padding:0;">${listBlock(trip.notes, '!', c.orange)}</ul>
      </div>
    </div>
    ${pageFooter(brand, trip, pageNum)}
  </section>`;
}

function buildContactPage(brand, trip, imgMap, pageNum) {
  const c = brand.colors;
  return `
  <section class="page" style="background:${c.navy};">
    ${img(imgMap, 'kathmandu_durbar', 'position:absolute;inset:0;width:100%;height:100%;opacity:.22;border-radius:0;')}
    <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(11,37,69,.65), rgba(11,37,69,.95));"></div>

    <div style="position:relative; z-index:2; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:0 90px;">
      <div style="background:#fff; border-radius:20px; padding:22px 42px; box-shadow:0 20px 44px rgba(0,0,0,.35);">
        <img src="${imgMap.__logo}" style="height:64px; display:block;" />
      </div>
      <p style="font-family:'Inter',sans-serif; font-weight:500; font-size:18px; color:#EAF0FB; max-width:560px; margin:28px 0 34px;">${esc(brand.tagline)}</p>

      <div style="display:flex; gap:18px;">
        <div class="pill" style="background:rgba(255,255,255,.08); border-color:${c.orange}; color:#fff;">${esc(brand.phone)}</div>
        <div class="pill" style="background:rgba(255,255,255,.08); border-color:${c.orange}; color:#fff;">WhatsApp ${esc(brand.whatsapp)}</div>
        <div class="pill" style="background:rgba(255,255,255,.08); border-color:${c.orange}; color:#fff;">${esc(brand.website)}</div>
      </div>
    </div>
    <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:${c.orange}; z-index:2;"></div>
  </section>`;
}

function buildHTML(trip, brand, assetsDir) {
  // resolve every referenced image key to a base64 data URI so the HTML is fully self-contained
  const imgMap = {};
  const imagesDir = path.join(assetsDir, 'images-optimized');
  for (const f of fs.readdirSync(imagesDir)) {
    const key = path.basename(f, path.extname(f));
    imgMap[key] = toDataUri(path.join(imagesDir, f));
  }
  imgMap.__logo = toDataUri(path.join(assetsDir, 'logo', 'farebuzzer-logo.png'));

  let pageNum = 1;
  const sections = [];
  sections.push(buildCover(brand, trip, imgMap));
  pageNum++;
  sections.push(buildOverview(brand, trip, imgMap, pageNum++));
  sections.push(buildCostPage(brand, trip, imgMap, pageNum++));
  for (const day of trip.days) {
    sections.push(buildDayPage(brand, trip, day, imgMap, pageNum++));
  }
  sections.push(buildInclusionsPage(brand, trip, imgMap, pageNum++));
  sections.push(buildContactPage(brand, trip, imgMap, pageNum++));

  return `<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>${esc(trip.title)} ${esc(trip.subtitle)}</title>
<style>${fontFaceCSS()}</style>
<style>${css(brand)}</style>
</head>
<body>
${sections.join('\n')}
</body>
</html>`;
}

module.exports = { buildHTML };
