const fs = require('fs');
const data = JSON.parse(fs.readFileSync('database/seeders/data/preset_routines_data.json', 'utf8'));

console.log('Total routines in json:', data.length);
let pmSunscreens = 0;
let amPmOnly = 0;
let variantsCount = 0;

data.forEach(r => {
  console.log(`\nRoutine #${r.id} - ${r.title_en} (${r.target_audience}):`);
  console.log('Items count:', r.items.length);
  if (r.usage_guide_json && (r.usage_guide_json.pm_variants || r.usage_guide_json.night_variants)) {
    const vars = r.usage_guide_json.pm_variants || r.usage_guide_json.night_variants;
    const keys = Array.isArray(vars) ? vars.map(v => v.id || v.label) : Object.keys(vars);
    console.log('Night variants keys:', keys.join(', '));
    variantsCount += keys.length;
  }
  r.items.forEach(item => {
    const isSunscreen = (item.product_name_ar || '').includes('واقي') || (item.category || '').toLowerCase().includes('sun');
    if (isSunscreen && item.night) {
      console.log('  VIOLATION: Sunscreen in PM:', item.product_name_ar);
      pmSunscreens++;
    }
    const isPmOnly = (item.use_time_ar || '').includes('مساءً فقط') || (!item.morning && item.night);
    if (isPmOnly && item.morning) {
      console.log('  VIOLATION: PM-only in AM:', item.product_name_ar);
      amPmOnly++;
    }
  });
});

console.log('\n--- F02 SUMMARY ---');
console.log('PM Sunscreens count:', pmSunscreens);
console.log('AM PM-Only Actives count:', amPmOnly);
console.log('Night Variants Total:', variantsCount);
if (pmSunscreens === 0 && amPmOnly === 0 && variantsCount > 0) {
  console.log('✅ ALL F02 AUDIT ACCEPTANCE CRITERIA MET PERFECTLY!');
} else {
  console.log('❌ Acceptance criteria failed!');
}
