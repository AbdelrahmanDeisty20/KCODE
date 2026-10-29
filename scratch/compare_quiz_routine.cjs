const fs = require('fs');
const zlib = require('zlib');
const { execSync } = require('child_process');

// 1. Extract HTML data
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

// Extract quiz questions from prototype
const htmlQuizQuestions = JSON.parse(decompressKey('0b8fe816a3d1ed9ce263'));

// Extract curated routines from prototype
const htmlRoutines = JSON.parse(decompressKey('baa10988b89ca5995ff4'));

// Extract men routines from prototype
// Let's find where men routines are stored
let htmlMenRoutines = null;
// Search in payload for men routines
function findMen(o, p = '') {
  if (typeof o === 'string' && /^H4sI/.test(o)) {
    try {
      const buf = zlib.gunzipSync(Buffer.from(o, 'base64')).toString('utf8');
      if (buf.includes('روتينات الرجال') || buf.includes('عناية الرجل')) {
        console.log('Found men routine reference in chunk:', p);
      }
    } catch(e) {}
  } else if (Array.isArray(o)) {
    o.forEach((v, i) => findMen(v, `${p}[${i}]`));
  } else if (o && typeof o === 'object') {
    for (const k in o) findMen(o[k], p ? `${p}.${k}` : k);
  }
}
findMen(payload);

// 2. Fetch Project DB Quiz Questions & Options
const dbQuizRaw = execSync('php -r "require \'vendor/autoload.php\'; $app = require \'bootstrap/app.php\'; $kernel = $app->make(\'Illuminate\\\\Contracts\\\\Console\\\\Kernel\'); $kernel->bootstrap(); echo App\\\\Models\\\\QuizQuestion::with(\'options\')->orderBy(\'step_number\')->get()->toJson();"', { cwd: 'c:/myProjects/KCODE', maxBuffer: 10 * 1024 * 1024 }).toString();
const dbQuiz = JSON.parse(dbQuizRaw);

// 3. Fetch Project DB Routine Goals
const dbGoalsRaw = execSync('php -r "require \'vendor/autoload.php\'; $app = require \'bootstrap/app.php\'; $kernel = $app->make(\'Illuminate\\\\Contracts\\\\Console\\\\Kernel\'); $kernel->bootstrap(); echo App\\\\Models\\\\RoutineGoal::all()->toJson();"', { cwd: 'c:/myProjects/KCODE', maxBuffer: 10 * 1024 * 1024 }).toString();
const dbGoals = JSON.parse(dbGoalsRaw);

// 4. Fetch Project DB Preset Routines
const dbPresetsRaw = execSync('php -r "require \'vendor/autoload.php\'; $app = require \'bootstrap/app.php\'; $kernel = $app->make(\'Illuminate\\\\Contracts\\\\Console\\\\Kernel\'); $kernel->bootstrap(); echo App\\\\Models\\\\PresetRoutine::with(\'products\')->get()->toJson();"', { cwd: 'c:/myProjects/KCODE', maxBuffer: 10 * 1024 * 1024 }).toString();
const dbPresets = JSON.parse(dbPresetsRaw);

console.log('=== COMPARISON REPORT ===');
console.log('\n--- 1. QUIZ QUESTIONS & OPTIONS ---');
console.log(`HTML Prototype Questions: ${htmlQuizQuestions.length}`);
console.log(`DB Questions: ${dbQuiz.length}`);

htmlQuizQuestions.forEach((hq, idx) => {
  console.log(`\n[HTML Step ${idx + 1}] Key: "${hq.key}" | Title: "${hq.title}" (Step Name: "${hq.step}")`);
  console.log(`   Hint: "${hq.hint || ''}"`);
  if (hq.helper) console.log(`   Helper: "${hq.helper}"`);
  console.log(`   Options (${hq.options.length}):`);
  hq.options.forEach((opt, oIdx) => {
    const [val, label, desc] = opt;
    console.log(`      ${oIdx + 1}. [${val}] "${label}" ${desc ? `-> (${desc})` : ''}`);
  });

  const matchingDbQ = dbQuiz.find(dq => dq.step_number === idx + 1);
  if (matchingDbQ) {
    console.log(`   >>> MATCHING IN DB (ID: ${matchingDbQ.id}, Step: ${matchingDbQ.step_number}):`);
    console.log(`       DB Title (AR): "${matchingDbQ.title_ar}"`);
    console.log(`       DB Title (EN): "${matchingDbQ.title_en}"`);
    console.log(`       DB Desc (AR): "${matchingDbQ.description_ar}"`);
    console.log(`       DB Type: "${matchingDbQ.selection_type}", Optional: ${matchingDbQ.is_optional}`);
    console.log(`       DB Options (${matchingDbQ.options.length}):`);
    matchingDbQ.options.forEach((doOpt, doIdx) => {
      console.log(`          ${doIdx + 1}. [ID: ${doOpt.id}, Type: ${doOpt.option_type}, Mapped: ${doOpt.mapped_id}] "${doOpt.title_ar}" / "${doOpt.title_en}" -> Desc: "${doOpt.description_ar || ''}"`);
    });
  } else {
    console.log(`   >>> NO MATCHING QUESTION IN DB FOR STEP ${idx + 1}!`);
  }
});

// Check if DB has extra questions
dbQuiz.forEach(dq => {
  if (dq.step_number > htmlQuizQuestions.length) {
    console.log(`\n[EXTRA DB QUESTION] Step: ${dq.step_number}, Title: "${dq.title_ar}"`);
  }
});

fs.writeFileSync('scratch/html_quiz_full.json', JSON.stringify(htmlQuizQuestions, null, 2));
fs.writeFileSync('scratch/db_quiz_full.json', JSON.stringify(dbQuiz, null, 2));
fs.writeFileSync('scratch/html_routines_full.json', JSON.stringify(htmlRoutines, null, 2));
fs.writeFileSync('scratch/db_presets_full.json', JSON.stringify(dbPresets, null, 2));
