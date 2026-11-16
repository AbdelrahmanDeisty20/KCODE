const fs = require('fs');
const html = fs.readFileSync('scratch_audit.html', 'utf8');

const regex = /<article class="finding\s*([^"]*)" id="(F\d\d|N\d\d)"[^>]*>([\s\S]*?)<\/article>/g;
let m;
const findings = [];
while ((m = regex.exec(html)) !== null) {
  const sev = m[1].trim();
  const id = m[2];
  const body = m[3];
  
  const titleMatch = body.match(/<h3>([\s\S]*?)<\/h3>/);
  const title = titleMatch ? titleMatch[1].replace(/<[^>]+>/g, '').trim() : '';
  
  const badgeMatch = body.match(/<span class="badge">([\s\S]*?)<\/span>/);
  const badge = badgeMatch ? badgeMatch[1].replace(/<[^>]+>/g, '').trim() : '';

  const mutedMatch = body.match(/<p class="muted">([\s\S]*?)<\/p>/);
  const muted = mutedMatch ? mutedMatch[1].replace(/<[^>]+>/g, '').trim() : '';

  const fieldRegex = /<div class="field"><strong>([\s\S]*?)<\/strong>([\s\S]*?)<\/div>/g;
  let fm;
  const fields = {};
  while ((fm = fieldRegex.exec(body)) !== null) {
    const fName = fm[1].replace(/<[^>]+>/g, '').trim();
    const fVal = fm[2].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    fields[fName] = fVal;
  }

  const evidenceMatch = body.match(/<div class="evidence">([\s\S]*?)<\/div>/);
  const evidence = evidenceMatch ? evidenceMatch[1].replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim() : '';

  findings.push({ id, sev, badge, title, muted, fields, evidence });
}

fs.writeFileSync('scratch_all_findings.json', JSON.stringify(findings, null, 2), 'utf8');
console.log('Successfully saved', findings.length, 'findings to scratch_all_findings.json');
