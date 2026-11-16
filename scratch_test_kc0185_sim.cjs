const fs = require('fs');

const policies = JSON.parse(fs.readFileSync('database/seeders/data/r479_quiz/usage_policies.json', 'utf8'));
const kc185Policy = policies['KC0185'];
console.log('KC0185 Policy:', kc185Policy);

// Simulate core products for acne|none|oily|no
const coreSkus = ['KC0003', 'KC0259', 'KC0002', 'KC0088', 'KC0159'];
console.log('Core SKUs:', coreSkus);
coreSkus.forEach(sku => {
  console.log(sku, policies[sku]);
});
