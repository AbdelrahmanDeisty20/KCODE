const fs = require('fs');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');

// Search for any api endpoints in html
const matches = html.match(/\/api\/[a-zA-Z0-9_\-\/]+/g);
console.log('API endpoints in HTML:', Array.from(new Set(matches || [])));
