const https = require('https');
const crypto = require('crypto');

function download(url) {
    return new Promise((resolve, reject) => {
        https.get(url, (res) => {
            const chunks = [];
            res.on('data', c => chunks.push(c));
            res.on('end', () => {
                const buf = Buffer.concat(chunks);
                const hash = crypto.createHash('md5').update(buf).digest('hex');
                resolve({ size: buf.length, hash, status: res.statusCode });
            });
        }).on('error', reject);
    });
}

async function main() {
    const urls = [
        'https://kcodeskin.com/storage/app/public/products/KC0002.webp',
        'https://kcodeskin.com/storage/app/public/products/KC0002_gallery_1.webp',
        'https://kcodeskin.com/storage/app/public/products/KC0002_gallery_2.webp'
    ];

    for (const u of urls) {
        const info = await download(u);
        console.log(u.split('/').pop(), info);
    }
}

main();
