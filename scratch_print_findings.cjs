const fs = require('fs');
const findings = JSON.parse(fs.readFileSync('scratch_all_findings.json', 'utf8'));

findings.forEach(f => {
  console.log(`\n======================================================`);
  console.log(`[${f.id}] ${f.badge} | ${f.title}`);
  console.log(`------------------------------------------------------`);
  console.log(`* المشاهد: ${f.fields['المشاهد'] || '—'}`);
  console.log(`* المرجع المطلوب: ${f.fields['المرجع المطلوب'] || '—'}`);
  console.log(`* الإصلاح المحدد: ${f.fields['الإصلاح المحدد'] || '—'}`);
  console.log(`* شرط القبول: ${f.fields['شرط القبول'] || '—'}`);
});
