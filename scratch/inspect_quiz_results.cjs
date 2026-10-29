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
console.log('quizResults length:', quizResultsContent.length);

const parsed = JSON.parse(quizResultsContent);
console.log('quizResults keys:', Object.keys(parsed));

// Let's inspect the structure of quizResults
console.log('Sample of quizResults:');
for (const k of Object.keys(parsed)) {
  if (Array.isArray(parsed[k])) {
    console.log(`Key ${k} is Array of length ${parsed[k].length}`);
    console.log(`First item in ${k}:`, JSON.stringify(parsed[k][0]).slice(0, 300));
  } else if (typeof parsed[k] === 'object') {
    console.log(`Key ${k} is Object with keys:`, Object.keys(parsed[k]).slice(0, 10));
  } else {
    console.log(`Key ${k}:`, parsed[k]);
  }
}
