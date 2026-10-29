const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

if (payload.pages.routines) {
  const rHtml = zlib.gunzipSync(Buffer.from(payload.pages.routines.inline, 'base64')).toString('utf8');
  console.log('routines HTML length:', rHtml.length);
  // Search for questions or selectors
  const matches = rHtml.match(/<script\b[^>]*>([\s\S]*?)<\/script>/gi);
  if (matches) {
    matches.forEach((s, idx) => {
      console.log(`Script ${idx}:`, s.slice(0, 150));
    });
  }
}
