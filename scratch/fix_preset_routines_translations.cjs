const fs = require('fs');

const path = 'database/seeders/data/preset_routines_data.json';
const data = JSON.parse(fs.readFileSync(path, 'utf8'));

// Official curated titles and descriptions with accurate professional English translations
const englishMap = {
  "روتين الحبوب والبثور وآثار الحبوب": {
    title_en: "Acne, Blemishes & Post-Acne Marks Routine",
    description_en: "A balanced routine targeting blemishes, redness, and excess sebum without neglecting skin hydration. Helps care for clogged pores and post-acne marks for clearer, smoother, and balanced skin.",
    skin_type_en: "Suitable for: Oily and combination skin prone to breakouts."
  },
  "روتين المسام والرؤوس السوداء واللمعان الزائد": {
    title_en: "Pores, Blackheads & Excess Shine Routine",
    description_en: "A focused routine for skin experiencing blackheads, enlarged pores, and rapid oiliness. Helps unclog pores, regulate sebum, and refine skin texture for a cleaner, smoother look.",
    skin_type_en: "Suitable for: Oily and combination skin."
  },
  "روتين البشرة الدهنية التي تشعر بالشد بعد التنظيف": {
    title_en: "Dehydrated Oily Skin Routine (Post-Cleanse Tightness)",
    description_en: "A specialized routine for oily and combination skin that experiences tightness or dehydration after washing. Balances excess oil while deeply replenishing lost moisture.",
    skin_type_en: "Suitable for: Oily and combination skin suffering from tightness or moisture deficit."
  },
  "روتين العناية بمظهر الكلف والتصبغات وتفاوت لون البشرة": {
    title_en: "Pigmentation, Melasma & Uneven Tone Care Routine",
    description_en: "An intensive brightening routine formulated to reduce the look of dark spots, pigmentation, and post-breakout discoloration, revealing a more uniform, luminous complexion.",
    skin_type_en: "Suitable for: Oily, combination, normal, and dry non-sensitive skin."
  },
  "روتين تهدئة الاحمرار ودعم حاجز البشرة": {
    title_en: "Redness Soothing & Skin Barrier Support Routine",
    description_en: "A gentle, comforting routine designed to calm visible irritation, reduce persistent redness, and fortify the weakened moisture barrier.",
    skin_type_en: "Suitable for: Sensitive skin, whether oily, combination, normal, or dry."
  },
  "روتين البشرة الجافة والباهتة للترطيب والإشراقة": {
    title_en: "Dry & Dull Skin Hydration & Radiance Routine",
    description_en: "A rich, deeply nourishing routine that restores optimal hydration, eliminates flakiness, and revives tired, lacklustre skin with healthy dewy radiance.",
    skin_type_en: "Suitable for: Dry skin and normal skin leaning towards dryness."
  },
  "روتين الخطوط الدقيقة والتجاعيد وفقدان المرونة والعناية بمحيط العين": {
    title_en: "Fine Lines, Wrinkles & Eye Contour Firming Routine",
    description_en: "An advanced age-defying routine targeted at early expression lines, firmness loss, and eye contour concerns like dark circles and puffiness.",
    skin_type_en: "Suitable for: Oily, combination, normal, and dry non-sensitive skin."
  },
  "روتين العناية اليومية للحفاظ على بشرة متوازنة ومشرقة": {
    title_en: "Daily Essential Balance & Radiance Routine",
    description_en: "A foundational daily regimen focused on maintaining healthy skin barrier function, optimal hydration, and a clean, naturally glowing appearance.",
    skin_type_en: "Suitable for: All skin types."
  }
};

let updated = 0;
data.forEach(r => {
  const trans = englishMap[r.title_ar];
  if (trans) {
    r.title_en = trans.title_en;
    r.description_en = trans.description_en;
    r.skin_type_en = trans.skin_type_en;
    updated++;
  }
});

fs.writeFileSync(path, JSON.stringify(data, null, 2), 'utf8');
console.log(`Updated ${updated} preset routines with accurate English translations.`);
