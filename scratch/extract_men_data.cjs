const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);
const menHtml = zlib.gunzipSync(Buffer.from(payload.pages.men.inline, 'base64')).toString('utf8');

const menDataMatch = menHtml.match(/<script type="application\/json" id="menData">([\s\S]*?)<\/script>/);
console.log('menData:', menDataMatch ? menDataMatch[1] : 'not found');
