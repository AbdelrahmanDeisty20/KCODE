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
    try {
        const html = await fetch('https://kcode-pied.vercel.app/product/2');
        console.log('HTML length:', html.length);
        const matches = [...html.matchAll(/src="([^"]+\.js)"/g)].map(m => m[1]);
        console.log('Scripts:', matches);

        for (const s of matches) {
            const scriptUrl = s.startsWith('http') ? s : 'https://kcode-pied.vercel.app' + s;
            console.log('Fetching script:', scriptUrl);
            const js = await fetch(scriptUrl);
            const apiMatches = js.match(/https?:\/\/[a-zA-Z0-9.-]+(?::[0-9]+)?(?:\/api)?/g);
            if (apiMatches) {
                console.log('API URLs:', [...new Set(apiMatches)]);
            }
            // Search for product image gallery logic
            const galleryIdx = js.indexOf('images');
            const productIdx = js.indexOf('/api/products');
            console.log('Gallery/Product index in JS:', { galleryIdx, productIdx });
        }
    } catch (err) {
        console.error(err);
    }
}

main();
