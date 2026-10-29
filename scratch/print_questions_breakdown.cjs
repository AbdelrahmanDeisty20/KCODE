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

const htmlQuiz = JSON.parse(decompressKey('0b8fe816a3d1ed9ce263'));
const dbQuiz = JSON.parse(fs.readFileSync('scratch/db_quiz.json', 'utf8'));

console.log('=== QUESTIONS DETAILED BREAKDOWN ===\n');

htmlQuiz.forEach((hq, idx) => {
  const stepNum = idx + 1;
  const dbQ = dbQuiz.find(q => q.step_number === stepNum);
  console.log(`\n======================================================`);
  console.log(`[السؤال ${stepNum}]: ${hq.title} (Key: ${hq.key}, Step: ${hq.step})`);
  console.log(`HTML Hint: "${hq.hint || ''}"`);
  if (hq.helper) console.log(`HTML Helper: "${hq.helper}"`);
  console.log(`DB Title: "${dbQ?.title_ar}" / "${dbQ?.title_en}"`);
  console.log(`DB Desc: "${dbQ?.description_ar}"`);
  
  console.log(`\n-- مقارنة الخيارات للسؤال ${stepNum} --`);
  console.log(`HTML Options Count: ${hq.options.length} | DB Options Count: ${dbQ?.options.length || 0}`);
  
  console.log('\nالخيارات في HTML:');
  hq.options.forEach((o, i) => {
    console.log(`  ${i+1}. [${o[0]}] "${o[1]}" ${o[2] ? `(${o[2]})` : ''}`);
  });
  
  console.log('\nالخيارات في DB:');
  dbQ?.options.forEach((o, i) => {
    console.log(`  ${i+1}. [ID:${o.id}, mapped_id:${o.mapped_id}, type:${o.option_type}] "${o.title_ar}" / "${o.title_en}" ${o.description_ar ? `(${o.description_ar})` : ''}`);
  });
});
