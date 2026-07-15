<?php
$filePath = __DIR__ . '/exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv';
$file = fopen($filePath, 'r');
$headers = fgetcsv($file);

$validCategories = ['Toner', 'Serum', 'Sunscreen', 'Cleanser', 'Essence', 'Moisturizer', 'Eye Care', 'Mist', 'Mask', 'Set', 'Treatment', 'Body Care'];

$shiftedCount = 0;
while (($row = fgetcsv($file)) !== false) {
    $data = array_combine($headers, $row);
    $cat = trim($data['category'] ?? '');
    if (!in_array($cat, $validCategories)) {
        echo "Shifted row found: ID=" . $data['product_id'] . " | Brand=" . $data['brand_en'] . " | Cat=" . $cat . "\n";
        $shiftedCount++;
    }
}
fclose($file);
echo "Total shifted rows: $shiftedCount\n";
