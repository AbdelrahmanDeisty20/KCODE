const fs = require('fs');
const html = fs.readFileSync('KCODE_BACKEND_ACTION_PLAN.html', 'utf8');
const regex = /<div class="finding-card[^"]*"[^>]*data-category="([^"]*)"[^>]*data-sev="([^"]*)"[^>]*data-id="([^"]*)"/g;
let m;
const backendFindings = [];
const allFindings = [];
while ((m = regex.exec(html)) !== null) {
    allFindings.push({ id: m[3], sev: m[2], cat: m[1] });
    if (m[1].includes('backend')) {
        backendFindings.push({ id: m[3], sev: m[2], cat: m[1] });
    }
}
console.log('All findings (' + allFindings.length + '):', allFindings.map(f => f.id).join(', '));
console.log('Backend findings (' + backendFindings.length + '):', backendFindings.map(f => f.id).join(', '));
