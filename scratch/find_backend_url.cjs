const https = require('https');

function fetch(url) {
    return new Promise((resolve, reject) => {
        https.get(url, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve(data));
        }).on('error', reject);
    });
}

async function main() {
    const js = await fetch('https://kcode-pied.vercel.app/assets/index-png-Ln14.js');
    console.log('JS length:', js.length);

    // Search for baseUrl or axios.create
    const axiosMatches = js.match(/baseURL:[^,}]+/g);
    console.log('baseURL matches:', axiosMatches);

    // Search for apiUrl
    const apiVarMatches = js.match(/[a-zA-Z0-9_]*API[a-zA-Z0-9_]*\s*[:=]\s*["'][^"']+["']/gi);
    console.log('API var matches:', apiVarMatches);

    // Search for all occurrences of kcodeskin
    const kcodeMatches = js.match(/https?:\/\/[^"'\s]*kcode[^"'\s]*/gi);
    console.log('kcode occurrences:', [...new Set(kcodeMatches)]);

    // Search for images array handling in product hero/gallery
    const heroIdx = js.indexOf('useProductHeroAction');
    console.log('heroIdx:', heroIdx);
    if (heroIdx !== -1) {
        console.log('Hero snippet:', js.slice(heroIdx - 200, heroIdx + 600));
    } else {
        // search for allImages or normalizeImgKey
        const normIdx = js.indexOf('normalizeImgKey');
        console.log('normIdx:', normIdx);
        if (normIdx !== -1) {
            console.log('Norm snippet:', js.slice(normIdx - 200, normIdx + 600));
        } else {
            // search for thumbnails or gallery
            const thumbIdx = js.indexOf('thumbnails');
            console.log('thumbIdx:', thumbIdx);
            if (thumbIdx !== -1) {
                console.log('Thumb snippet:', js.slice(thumbIdx - 100, thumbIdx + 400));
            }
        }
    }
}

main();
