const fs = require('fs');
const json = JSON.parse(fs.readFileSync('database/seeders/data/preset_routines_data.json', 'utf8'));

json.forEach((r, idx) => {
    const str = JSON.stringify(r);
    if (str.includes('ويجب أن يعكس الموقع') || str.includes('لا يُكتب أن الواقي')) {
        console.log('Index:', idx, 'Title:', r.title_ar, 'Slug:', r.slug);
        for (const [k, v] of Object.entries(r)) {
            const vStr = JSON.stringify(v);
            if (vStr.includes('ويجب أن يعكس الموقع') || vStr.includes('لا يُكتب أن الواقي')) {
                console.log(`  Key [${k}]:`, v);
            }
        }
    }
});
