# FareBuzzer Itinerary Generator

Turns a JSON itinerary into a branded, landscape (16:9) PDF brochure. Fully self-contained render (fonts + images inlined as base64) so it works offline.

## Reuse for a new trip

1. Copy `data/nepal-muktinath.json` to `data/<trip-name>.json` and edit the content (destinations, hotels, days, inclusions, etc.). `data/brand.json` holds the FareBuzzer brand config (colors, logo, contact) and doesn't need to change between trips.
2. Drop any new photos into `assets/images/`, then run `node optimize-images.js` to resize/compress them into `assets/images-optimized/` (that's what the template actually embeds). Reference them by filename (no extension) from the trip JSON's `images`/`coverImages` fields.
3. Build:
   ```
   node build.js <trip-name>
   ```
   Produces `output/<trip-name>.html` and `output/<trip-name>.pdf`.
4. Optional: `node preview.js <trip-name>` renders each page as a PNG in `output/previews/` for a quick visual QA pass before sending the PDF.

## Layout / branding

All markup and styling lives in `template/render.js` — one function per page type (cover, overview, cost, day, inclusions, contact). Edit there to change layout; edit `data/brand.json` to change colors/logo/contact info everywhere at once.

Requires a local Chrome or Edge install (used headless via `puppeteer-core`, no bundled Chromium download). Override the path with `CHROME_PATH` if it's not in the default install location.
