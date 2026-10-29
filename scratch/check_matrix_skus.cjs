const fs = require('fs');

const matrix = JSON.parse(fs.readFileSync('database/seeders/data/quiz_results_matrix.json', 'utf8'));
console.log('Matrix products count:', matrix.products.length);

const skusInMatrix = new Set(matrix.products.map(p => p.sku));
console.log('Unique SKUs in Matrix:', skusInMatrix.size);

// Check against DB products
const { execSync } = require('child_process');
const dbSkusRaw = execSync('php -r "require \'vendor/autoload.php\'; $app = require \'bootstrap/app.php\'; $kernel = $app->make(\'Illuminate\\\\Contracts\\\\Console\\\\Kernel\'); $kernel->bootstrap(); echo json_encode(App\\\\Models\\\\Product::pluck(\'sku\')->toArray());"', { cwd: 'c:/myProjects/KCODE' }).toString();

// In Windows, let's read via dump script or file if parse error
console.log('Matrix unique SKUs:');
console.log(Array.from(skusInMatrix).join(', '));
