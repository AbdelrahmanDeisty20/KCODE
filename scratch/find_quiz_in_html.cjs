const fs = require('fs');
const zlib = require('zlib');
const path = require('path');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
console.log('Reading HTML file...');
const html = fs.readFileSync(src, 'utf8');
console.log('HTML size:', (html.length / (1024 * 1024)).toFixed(2), 'MB');

// Search for keywords related to routine and quiz
// مثل: "routine", "quiz", "question", "step", "skin_type", "concern", "goal", "أسئلة", "بشرتك"
const keywords = [
  'quiz', 'routine', 'question', 'step', 'goal', 'concern', 'skin_type',
  'بشرت', 'السؤال', 'الهدف', 'روتين', 'الخطوة', 'نوع البشرة', 'حساس'
];

// Let's inspect the unpacked payload files if they exist or unpack them
const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
if (m) {
  console.log('Found kcode-payload script');
  const payload = JSON.parse(m[1]);
  console.log('Payload keys:', Object.keys(payload));

  // Recursively search for objects/arrays that look like quiz or routine questions
  function search(obj, path = '') {
    if (!obj) return;
    if (typeof obj === 'string') {
      if (/^H4sI/.test(obj)) {
        try {
          const buf = zlib.gunzipSync(Buffer.from(obj, 'base64')).toString('utf8');
          // Check if this decompressed text contains quiz/routine
          if (buf.includes('quiz') || buf.includes('question') || buf.includes('بشرت') || buf.includes('روتين')) {
            console.log(`Match in gzip chunk: ${path} (len: ${buf.length})`);
            // Search inside buf
            searchInText(buf, path);
          }
        } catch (e) {}
      }
    } else if (Array.isArray(obj)) {
      obj.forEach((item, idx) => search(item, `${path}[${idx}]`));
    } else if (typeof obj === 'object') {
      for (const [k, v] of Object.entries(obj)) {
        search(v, `${path}.${k}`);
      }
    }
  }

  function searchInText(text, p) {
    // Look for JSON structures inside text
    // E.g., strings containing "options", "questions", "steps", etc.
    const sample = [];
    const lines = text.split('\n');
    for (let i = 0; i < lines.length; i++) {
      const line = lines[i];
      if (line.includes('question') || line.includes('quiz') || line.includes('بشرت') || line.includes('روتين')) {
        sample.push(`L${i}: ${line.slice(0, 150)}`);
        if (sample.length > 20) break;
      }
    }
    console.log(`Sample lines in ${p}:\n`, sample.join('\n'));
  }

  search(payload);
} else {
  console.log('No kcode-payload script found directly, searching inline scripts...');
  const scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
  let match;
  let sIdx = 0;
  while ((match = scriptRegex.exec(html)) !== null) {
    sIdx++;
    const content = match[1];
    if (content.includes('quiz') || content.includes('routine') || content.includes('بشرت')) {
      console.log(`Script #${sIdx} contains keywords! Length:`, content.length);
    }
  }
}
