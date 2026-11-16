const fs = require('fs');

const html = fs.readFileSync('KCODE_Final_Test_Report_2026-11-16.html', 'utf8');

// Strip tags and print sections
const plain = html.replace(/<style[\s\S]*?<\/style>/gi, '')
                  .replace(/<script[\s\S]*?<\/script>/gi, '');

// Find all finding / table sections
const itemsRegex = /(F\d+|N\d+)[^\n<]*[\s\S]*?(?=(?:F\d+|N\d+)|$)/g;

fs.writeFileSync('scratch/final_report_plain.txt', plain.replace(/<[^>]+>/g, '\n').replace(/\n\s*\n+/g, '\n'), 'utf8');
console.log('Saved scratch/final_report_plain.txt');
