const https = require('https');

function fetch(url) {
    return new Promise((resolve, reject) => {
        https.get(url, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve({ status: res.statusCode, data }));
        }).on('error', reject);
    });
}

async function main() {
    try {
        console.log('Fetching https://kcodeskin.com/api/products/2 ...');
        const res = await fetch('https://kcodeskin.com/api/products/2');
        console.log('Status:', res.status);
        const json = JSON.parse(res.data);
        console.log('Keys in data:', Object.keys(json.data || {}));
        console.log('image:', json.data?.image);
        console.log('images:', json.data?.images);
        console.log('product_images:', json.data?.product_images);
    } catch (e) {
        console.error('Error fetching product 2:', e);
    }
}

main();
