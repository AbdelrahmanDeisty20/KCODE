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
const htmlCurated = JSON.parse(decompressKey('baa10988b89ca5995ff4'));
const htmlMen = JSON.parse(decompressKey('29329431bbd6ca075eb8'));

const dbQuiz = JSON.parse(fs.readFileSync('scratch/db_quiz.json', 'utf8'));
const dbGoals = JSON.parse(fs.readFileSync('scratch/db_goals.json', 'utf8'));
const dbPresets = JSON.parse(fs.readFileSync('scratch/db_presets.json', 'utf8'));
const dbConcerns = JSON.parse(fs.readFileSync('scratch/db_concerns.json', 'utf8'));
const dbSkinTypes = JSON.parse(fs.readFileSync('scratch/db_skin_types.json', 'utf8'));

console.log('================================================================');
console.log('                 مقارنة تفصيلية بين ملف الويب والمشروع           ');
console.log('================================================================\n');

console.log('1. مقارنة أسئلة واختيارات الاستبيان / تحليل البشرة (Quiz Questions & Options):');
console.log('----------------------------------------------------------------------');
console.log(`- عدد الأسئلة في ملف الويب (HTML): ${htmlQuiz.length}`);
console.log(`- عدد الأسئلة في قاعدة بيانات المشروع (DB): ${dbQuiz.length}\n`);

htmlQuiz.forEach((hq, qIndex) => {
  const stepNum = qIndex + 1;
  const dbQ = dbQuiz.find(q => q.step_number === stepNum);

  console.log(`\n================== [ السؤال رقم ${stepNum} في الـ HTML ] ==================`);
  console.log(`المفتاح البرمجي (Key): "${hq.key}"`);
  console.log(`مسمى الخطوة (Step): "${hq.step}"`);
  console.log(`نص السؤال (Title): "${hq.title}"`);
  console.log(`التلميح (Hint): "${hq.hint || 'لا يوجد'}"`);
  if (hq.helper) console.log(`المساعد/التوضيح الإضافي (Helper): "${hq.helper}"`);
  console.log(`عدد الخيارات في الـ HTML: ${hq.options.length}`);
  
  if (!dbQ) {
    console.log(`\n❌ تنبيه: هذا السؤال غير موجود إطلاقاً في قاعدة بيانات المشروع!`);
    return;
  }

  console.log(`\n--- المقابل له في قاعدة بيانات المشروع (DB ID: ${dbQ.id}) ---`);
  console.log(`العنوان عربي: "${dbQ.title_ar}"`);
  console.log(`العنوان إنجليزي: "${dbQ.title_en}"`);
  console.log(`الوصف عربي: "${dbQ.description_ar || 'لا يوجد'}"`);
  console.log(`الوصف إنجليزي: "${dbQ.description_en || 'لا يوجد'}"`);
  console.log(`نوع الاختيار: "${dbQ.selection_type}" (single/multiple)`);
  console.log(`اختياري؟: ${dbQ.is_optional ? 'نعم (Optional)' : 'لا (Required)'}`);
  console.log(`عدد الخيارات في الـ DB: ${dbQ.options.length}`);

  console.log(`\n--- مقارنة الخيارات التفصيلية للسؤال ${stepNum} ---`);
  
  // Compare options
  console.log('\n[خيارات الـ HTML]:');
  hq.options.forEach((opt, oIdx) => {
    const val = opt[0];
    const textAr = opt[1];
    const desc = opt[2] || '';
    console.log(`  ${oIdx + 1}. [Key: ${val}] "${textAr}" ${desc ? `| توضيح: "${desc}"` : ''}`);
  });

  console.log('\n[خيارات الـ DB في المشروع]:');
  dbQ.options.forEach((opt, oIdx) => {
    console.log(`  ${oIdx + 1}. [ID: ${opt.id}, mapped_id: ${opt.mapped_id}, type: ${opt.option_type}]`);
    console.log(`     عربي: "${opt.title_ar}" | وصف: "${opt.description_ar || ''}"`);
    console.log(`     إنجليزي: "${opt.title_en}" | وصف: "${opt.description_en || ''}"`);
  });

  // Check differences
  console.log('\n[الفروقات الدقيقة]:');
  // Check count difference
  if (hq.options.length !== dbQ.options.length) {
    console.log(`  ⚠️ اختلاف عدد الخيارات: في الـ HTML عددها ${hq.options.length} وفي الـ DB عددها ${dbQ.options.length}`);
  } else {
    console.log(`  ✅ عدد الخيارات متطابق (${hq.options.length})`);
  }
});

// Let's also compare Routines
console.log('\n\n================================================================');
console.log('2. مقارنة الروتينات الجاهزة (Preset / Curated Routines):');
console.log('----------------------------------------------------------------');
console.log(`- عدد الروتينات في الـ HTML (Curated): ${htmlCurated.routines.length}`);
console.log(`- عدد روتينات الرجال في الـ HTML (Men): ${htmlMen.routines.length}`);
console.log(`- عدد الروتينات في الـ DB (PresetRoutine): ${dbPresets.length} (منها رجال: ${dbPresets.filter(p => p.is_for_men).length}, نساء: ${dbPresets.filter(p => !p.is_for_men).length})`);

console.log('\nقائمة روتينات الـ HTML (الأساسية):');
htmlCurated.routines.forEach((r, idx) => {
  console.log(`  ${idx + 1}. ID: "${r.id}" | العنوان: "${r.title}" | عدد المنتجات: ${r.products.length}`);
  console.log(`     الفئة المناسبة (Fit): "${r.fit}"`);
});

console.log('\nقائمة روتينات الرجال في الـ HTML:');
htmlMen.routines.forEach((r, idx) => {
  console.log(`  ${idx + 1}. ID: "${r.id}" | العنوان: "${r.title}" | عدد المنتجات: ${r.items ? r.items.length : (r.products ? r.products.length : 0)}`);
});

console.log('\nقائمة الروتينات في الـ DB:');
dbPresets.forEach((p, idx) => {
  console.log(`  ${idx + 1}. [ID: ${p.id}] "${p.title_ar}" (رجال؟ ${p.is_for_men ? 'نعم' : 'لا'}) | عدد المنتجات: ${p.items.length}`);
});

// Compare Routine Goals
console.log('\n\n================================================================');
console.log('3. مقارنة أهداف الروتين (Routine Goals):');
console.log('----------------------------------------------------------------');
console.log(`- خيارات السؤال الأول (هدفك الأساسي) في الـ HTML: ${htmlQuiz[0].options.length}`);
console.log(`- أهداف الروتين في الـ DB (RoutineGoal): ${dbGoals.length}`);
console.log('\nخيارات السؤال الأول في الـ HTML:');
htmlQuiz[0].options.forEach(([val, label]) => {
  const matchGoal = dbGoals.find(g => g.name_ar.includes(label) || label.includes(g.name_ar));
  console.log(`  - [${val}] "${label}" ${matchGoal ? `-> مطابق للهدف بالـ DB ID ${matchGoal.id} ("${matchGoal.name_ar}")` : '-> ⚠️ غير مطابق بالاسم حرفياً'}`);
});
console.log('\nالأهداف في جدول routine_goals بالـ DB:');
dbGoals.forEach(g => {
  console.log(`  - [ID: ${g.id}] "${g.name_ar}" / "${g.name_en}"`);
});
