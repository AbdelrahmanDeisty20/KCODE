const fs = require('fs');
const zlib = require('zlib');
const path = require('path');

const src = 'C:/Users/Dell/Downloads/KCODE_R422_I_DESKTOP (2).html';
const html = fs.readFileSync(src, 'utf8');

const m = html.match(/<script id="kcode-payload" type="application\/json">([\s\S]*?)<\/script>/);
const payload = JSON.parse(m[1]);

const chunks = [];
function findChunks(obj, p = '') {
  if (typeof obj === 'string') {
    if (/^H4sI/.test(obj)) {
      try {
        const buf = zlib.gunzipSync(Buffer.from(obj, 'base64')).toString('utf8');
        chunks.push({ path: p, content: buf });
      } catch (e) {}
    }
  } else if (Array.isArray(obj)) {
    obj.forEach((item, idx) => findChunks(item, `${p}[${idx}]`));
  } else if (typeof obj === 'object') {
    for (const [k, v] of Object.entries(obj)) {
      findChunks(v, p ? `${p}.${k}` : k);
    }
  }
}
findChunks(payload);

console.log(`Found ${chunks.length} decompressed chunks.`);

// Search in chunks for quiz questions / routine data
for (const c of chunks) {
  // Let's see if this chunk contains quiz questions or options
  if (c.content.includes('هل تتهيّج') || c.content.includes('نوع بشرتك') || c.content.includes('هدف') || c.content.includes('quiz') || c.content.includes('questions')) {
    // Check if it has question definitions
    // Look for keywords: "step", "options", "question"
    console.log(`--- Chunk ${c.path} (length: ${c.content.length}) ---`);
    // Find snippets
    const lines = c.content.split('\n');
    lines.forEach((l, idx) => {
      if (l.includes('step') && (l.includes('title') || l.includes('options') || l.includes('question') || l.includes('id:'))) {
        console.log(`[L${idx}]`, l.slice(0, 300));
      }
      if (l.includes('questions=') || l.includes('QUIZ=') || l.includes('QUESTIONS=') || l.includes('quizQuestions')) {
        console.log(`[L${idx} MATCH VAR]`, l.slice(0, 300));
      }
    });
  }
}
