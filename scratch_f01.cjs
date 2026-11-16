const fs = require('fs');
const html = fs.readFileSync('scratch_audit.html', 'utf8');
const f01Start = html.indexOf('id="F01"');
const f01End = html.indexOf('</article>', f01Start);
console.log(html.substring(f01Start, f01End));
