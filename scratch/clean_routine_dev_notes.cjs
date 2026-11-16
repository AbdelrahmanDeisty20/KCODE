const fs = require('fs');

const path = 'database/seeders/data/preset_routines_data.json';
let text = fs.readFileSync(path, 'utf8');

// Replace acne developer note
text = text.replace(
    /يُستخدم COSRX BHA مساءً 2–3 مرات أسبوعياً مع التدرج، ويجب أن يعكس الموقع تكرار الاستخدام الوارد في بيانات المنتج\./g,
    'يُستخدم COSRX BHA مساءً 2–3 مرات أسبوعياً مع التدرج حسب تحمّل بشرتك.'
);

// Replace pigmentation developer note
text = text.replace(
    /يعرض ANUA صباحاً أو في الليالي التي لا يُستخدم فيها Retino-Mela\. يعرض Retino-Mela مساءً فقط وفق التدرج المسجل\. لا يُعرض السيرومان في الجلسة نفسها\. لا يُكتب أن الواقي ملوّن أو يحتوي أكاسيد الحديد أو يوفر حماية خاصة من الضوء المرئي\./g,
    'يُستخدم سيروم ANUA صباحاً أو في الليالي التي لا يُستخدم فيها Retino-Mela، بينما يُستخدم Retino-Mela مساءً فقط وفق التدرج المسجل. تجنب استخدام السيرومين معاً في نفس الجلسة.'
);

fs.writeFileSync(path, text, 'utf8');
console.log('Successfully cleaned developer notes in preset_routines_data.json!');
