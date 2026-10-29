const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

function decompressKey(key) {
  // Traverse payload to find key
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

const quizQuestionsContent = decompressKey('0b8fe816a3d1ed9ce263');
console.log('=== QUIZ QUESTIONS (data.0b8fe816a3d1ed9ce263) ===');
try {
  const parsed = JSON.parse(quizQuestionsContent);
  console.log(JSON.stringify(parsed, null, 2));
} catch(e) {
  console.log(quizQuestionsContent);
}

const routinesContent = decompressKey('baa10988b89ca5995ff4');
console.log('=== ROUTINES / CURATED (data.baa10988b89ca5995ff4) ===');
try {
  const parsedRoutines = JSON.parse(routinesContent);
  console.log(JSON.stringify(parsedRoutines, null, 2).slice(0, 3000));
} catch(e) {
  console.log(routinesContent?.slice(0, 2000));
}
