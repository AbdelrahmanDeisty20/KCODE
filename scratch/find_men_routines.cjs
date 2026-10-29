const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

// Let's inspect pages.men.inline
if (payload.pages.men) {
  const menHtml = zlib.gunzipSync(Buffer.from(payload.pages.men.inline, 'base64')).toString('utf8');
  console.log('menHtml length:', menHtml.length);
  // Look for data scripts
  const scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
  let match;
  while ((match = scriptRegex.exec(menHtml)) !== null) {
    const fullTag = match[0].slice(0, 100);
    const content = match[1];
    if (content.length > 50 && (content.includes('routines') || content.includes('products') || content.includes('title'))) {
      console.log('Men script:', fullTag, content.slice(0, 200));
    }
  }
}

// Let's also inspect chunk data.d69e099076cfba69da02.inline
function decompressKey(key) {
  let found = null;
  function walk(o, p = '') {
    if (p.includes(key)) found = o;
    if (o && typeof o === 'object') {
      for (const k in o) walk(o[k], p ? p + '.' + k : k);
    }
  }
  walk(payload);
  if (found && typeof found === 'string' && /^H4sI/.test(found)) {
    return zlib.gunzipSync(Buffer.from(found, 'base64')).toString('utf8');
  }
  return null;
}

const d69 = decompressKey('d69e099076cfba69da02');
console.log('d69 length:', d69 ? d69.length : 0);
if (d69) {
  console.log(d69.slice(0, 500));
}
