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

const curated = JSON.parse(decompressKey('baa10988b89ca5995ff4'));
console.log('Total curated routines:', curated.routines.length);

curated.routines.forEach((r, idx) => {
  console.log(`\n=== Routine ${idx+1}: [${r.id}] ${r.title} ===`);
  console.log('Fit:', r.fit);
  console.log('Products Count:', r.products.length);
  r.products.forEach((p, pIdx) => {
    console.log(`   ${pIdx+1}. SKU: ${p.sku} | Name: ${p.source_name} | Optional: ${p.optional} | Support Copy: ${p.support_copy || 'none'}`);
  });
});

fs.writeFileSync('scratch/html_curated_all.json', JSON.stringify(curated.routines, null, 2));
