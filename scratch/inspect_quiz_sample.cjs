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

const quizResults = JSON.parse(decompressKey('b3c7977b30e7a6a0dc4f'));
console.log('Total products in quizResults:', quizResults.products.length);

const samplePath = quizResults.paths['acne|oily|yes|blackheads'];
console.log('Sample Path:', samplePath);

console.log('Primary products:');
samplePath.primary_routine.forEach(idx => {
  const p = quizResults.products[idx];
  console.log(`  [${idx}] SKU: ${p.sku} | Name: ${p.product_name} | Step: ${p.routine_step_ar} | Use: ${p.use_time_ar} | Badge: ${p.usage_badge_ar}`);
});

console.log('Support products:');
samplePath.routine_support.forEach(idx => {
  const p = quizResults.products[idx];
  console.log(`  [${idx}] SKU: ${p.sku} | Name: ${p.product_name} | Step: ${p.routine_step_ar} | Use: ${p.use_time_ar} | Badge: ${p.usage_badge_ar}`);
});

console.log('Cart addons:');
samplePath.cart_addons.forEach(idx => {
  const p = quizResults.products[idx];
  console.log(`  [${idx}] SKU: ${p.sku} | Name: ${p.product_name}`);
});
