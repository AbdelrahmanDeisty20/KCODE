const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

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

const quizResultsContent = decompressKey('b3c7977b30e7a6a0dc4f');
const parsed = JSON.parse(quizResultsContent);

console.log('Total paths count:', Object.keys(parsed.paths).length);

const firstKey = Object.keys(parsed.paths)[0];
console.log(`Path [${firstKey}]:`);
console.log(JSON.stringify(parsed.paths[firstKey], null, 2));
