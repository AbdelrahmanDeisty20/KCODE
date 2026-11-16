const fs = require('fs');

const updated = JSON.parse(fs.readFileSync('database/seeders/data/preset_routines_data.json', 'utf8'));
const g = JSON.parse(fs.readFileSync('scratch_technical/r479_general_routines.txt', 'utf8'));

console.log('Testing 8 Preset Routines Timing & Specifications...');
let totalProducts = 0;
let sunscreenInPm = 0;
let pmOnlyInAm = 0;
let missingUsageGuide = 0;
let missingHowToUse = 0;
let missingVariants = 0;

updated.forEach((r, idx) => {
  console.log(`\nRoutine #${idx + 1}: ${r.title_ar} (${r.slug})`);
  if (!r.usage_guide_json) missingUsageGuide++;
  if (!r.how_to_use_ar) missingHowToUse++;
  if (!r.usage_guide_json?.pm_variants) missingVariants++;

  console.log(`  AM Steps: ${r.usage_guide_json?.am_steps?.length || 0}`);
  console.log(`  PM Standard Steps: ${r.usage_guide_json?.pm_standard_steps?.length || 0}`);
  console.log(`  PM Variants: ${r.usage_guide_json?.pm_variants?.map(v => v.label).join(', ') || 'None'}`);

  r.items.forEach(item => {
    totalProducts++;
    const meta = g.products[item.sku] || {};
    const isSunscreen = meta.category === 'Sunscreen';
    const isPmOnly = meta.zone === 'PM';

    if (isSunscreen && item.night) {
      sunscreenInPm++;
      console.log(`  ❌ VIOLATION: Sunscreen ${item.sku} has night:true`);
    }
    if (isPmOnly && item.morning) {
      pmOnlyInAm++;
      console.log(`  ❌ VIOLATION: PM-only ${item.sku} has morning:true`);
    }

    console.log(`    - [${item.sku}] ${meta.name} | Morning: ${item.morning} | Night: ${item.night} | Freq: ${item.frequency_ar}`);
  });
});

console.log('\n=================================================');
console.log('FINAL AUDIT CHECK (F02 Acceptance Criteria):');
console.log('Total Products Checked:', totalProducts);
console.log('Sunscreens in PM:', sunscreenInPm, '(Target: 0)');
console.log('PM-only actives in AM:', pmOnlyInAm, '(Target: 0)');
console.log('Missing Usage Guides:', missingUsageGuide, '(Target: 0)');
console.log('Missing How To Use:', missingHowToUse, '(Target: 0)');

if (sunscreenInPm === 0 && pmOnlyInAm === 0 && missingUsageGuide === 0 && missingHowToUse === 0) {
  console.log('🎉 100% SUCCESS! F02 IS COMPLETELY RESOLVED AND AUDIT-PROOF!');
} else {
  console.log('⚠️ ERRORS FOUND!');
}
console.log('=================================================');
