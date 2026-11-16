const fs = require('fs');
const html = fs.readFileSync('KCODE_Final_Test_Report_2026-11-16.html', 'utf8');

const idx = html.indexOf('F16');
if (idx !== -1) {
    const snippet = html.slice(idx, idx + 3500);
    console.log(snippet.replace(/<[^>]+>/g, '\n').replace(/\n\s*\n+/g, '\n'));
} else {
    console.log('F16 not found');
}
