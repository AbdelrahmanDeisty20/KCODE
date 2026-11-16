const fs = require('fs');
const p = JSON.parse(fs.readFileSync('database/seeders/data/r479_products.json', 'utf8'));
const skus = ['KC0011', 'KC0023', 'KC0063', 'KC0068', 'KC0084', 'KC0089', 'KC0127', 'KC0129', 'KC0145', 'KC0153', 'KC0158', 'KC0178', 'KC0054', 'KC0122', 'KC0124'];

skus.forEach(s => {
  const prod = p[s];
  if (!prod) return console.log(s, 'NOT FOUND');
  const m = prod.summaryHtml ? prod.summaryHtml.match(/<strong class="fit-value fit-status">([^<]+)<\/strong>/) : null;
  console.log(s, '=>', m ? m[1] : 'NONE');
});
