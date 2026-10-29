const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

function searchInPayload(obj, p = '') {
  if (typeof obj === 'string') {
    if (obj.includes('quizQuestions') || obj.includes('quizResults')) {
      console.log(`Match in raw string at ${p}`);
    }
    if (/^H4sI/.test(obj)) {
      try {
        const buf = zlib.gunzipSync(Buffer.from(obj, 'base64')).toString('utf8');
        if (buf.includes('quizQuestions') || buf.includes('quizResults')) {
          console.log(`Match in decompressed chunk at ${p}`);
        }
      } catch (e) {}
    }
  } else if (Array.isArray(obj)) {
    obj.forEach((v, i) => searchInPayload(v, `${p}[${i}]`));
  } else if (obj && typeof obj === 'object') {
    for (const k in obj) searchInPayload(obj[k], p ? `${p}.${k}` : k);
  }
}
searchInPayload(payload);
