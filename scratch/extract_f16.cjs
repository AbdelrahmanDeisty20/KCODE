const fs = require('fs');
const html = fs.readFileSync('KCODE_Final_Test_Report_2026-11-16.html', 'utf8');

// Search for F16 in full text
let pos = 0;
while (true) {
    const idx = html.indexOf('F16', pos);
    if (idx === -1) break;
    console.log('--- F16 match at pos:', idx);
    console.log(html.slice(Math.max(0, idx - 100), idx + 1000).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' '));
    pos = idx + 10;
}
