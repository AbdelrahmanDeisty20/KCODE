const fs = require('fs');
const zlib = require('zlib');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

const quizHtml = zlib.gunzipSync(Buffer.from(payload.pages.quiz.inline, 'base64')).toString('utf8');
console.log('=== pages.quiz.inline length:', quizHtml.length);

// Let's find script tags or data inside quizHtml
const scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
let match;
while ((match = scriptRegex.exec(quizHtml)) !== null) {
  const fullTag = match[0];
  const tagOpen = fullTag.slice(0, fullTag.indexOf('>') + 1);
  const content = match[1];
  console.log(`Script tag: ${tagOpen}, length: ${content.length}`);
  if (tagOpen.includes('quizQuestions') || tagOpen.includes('quizResults')) {
    console.log(`Content of ${tagOpen}:`);
    console.log(content.slice(0, 1000));
  }
}

// Also check for any other quiz or routine data in quizHtml
fs.writeFileSync('scratch/quiz_page_extracted.html', quizHtml);
console.log('Saved to scratch/quiz_page_extracted.html');
