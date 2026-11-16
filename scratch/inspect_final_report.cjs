const fs = require('fs');

const html = fs.readFileSync('KCODE_Final_Test_Report_2026-11-16.html', 'utf8');

console.log('HTML size:', html.length);

// Extract title and headers
const titleMatch = html.match(/<title>([^<]+)<\/title>/i);
console.log('Title:', titleMatch ? titleMatch[1] : 'No title');

// Strip HTML tags for quick overview of first 2000 characters
const plain = html.replace(/<style[\s\S]*?<\/style>/gi, '')
                  .replace(/<script[\s\S]*?<\/script>/gi, '')
                  .replace(/<[^>]+>/g, ' ')
                  .replace(/\s+/g, ' ');

console.log('--- PREVIEW (First 2500 chars) ---');
console.log(plain.slice(0, 2500));
