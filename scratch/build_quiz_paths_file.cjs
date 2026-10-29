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

const quizResultsRaw = decompressKey('b3c7977b30e7a6a0dc4f');
const quizResults = JSON.parse(quizResultsRaw);

// Translate products usage badges and step names if needed
const outPath = 'database/seeders/data/quiz_results_matrix.json';
fs.writeFileSync(outPath, JSON.stringify(quizResults, null, 2), 'utf8');
console.log(`Saved quiz_results_matrix.json (${(fs.statSync(outPath).size / 1024).toFixed(1)} KB)`);
