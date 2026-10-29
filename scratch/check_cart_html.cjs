const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

if (payload.pages.cart) {
  const cHtml = zlib.gunzipSync(Buffer.from(payload.pages.cart.inline, 'base64')).toString('utf8');
  console.log('Cart HTML length:', cHtml.length);
  // Find cart empty states
  const lines = cHtml.split('\n');
  lines.forEach(l => {
    if (l.includes('empty') || l.includes('فارغ') || l.includes('سلتك') || l.includes('لا توجد منتجات')) {
      console.log('Match in Cart HTML:', l.slice(0, 150));
    }
  });
}
