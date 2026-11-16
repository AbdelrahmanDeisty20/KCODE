const fs = require('fs');

const path = 'database/seeders/product_images_map.json';
const map = JSON.parse(fs.readFileSync(path, 'utf8'));

let modifiedCount = 0;
let totalRemoved = 0;

for (const [sku, data] of Object.entries(map)) {
    if (data.subs && data.subs.length > 0) {
        const originalLength = data.subs.length;
        data.subs = data.subs.filter(s => !s.includes('_gallery_1.webp'));
        if (data.subs.length !== originalLength) {
            modifiedCount++;
            totalRemoved += (originalLength - data.subs.length);
        }
    }
}

fs.writeFileSync(path, JSON.stringify(map, null, 2), 'utf8');
console.log(`Cleaned ${path}: modified ${modifiedCount} SKUs, removed ${totalRemoved} duplicate gallery_1 entries.`);
