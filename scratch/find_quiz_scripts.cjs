const fs = require('fs');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');

// Find all script tags with id containing quiz or routine
const regex = /<script\b[^>]*id="([^"]*)"[^>]*>([\s\S]*?)<\/script>/gi;
let match;
while ((match = regex.exec(html)) !== null) {
  const id = match[1];
  const content = match[2];
  if (id.toLowerCase().includes('quiz') || id.toLowerCase().includes('routine')) {
    console.log(`Found script id="${id}", length: ${content.length}`);
    if (content.length < 5000) {
      console.log(content);
    } else {
      console.log(content.slice(0, 1000) + '\n... [truncated]');
    }
  }
}
