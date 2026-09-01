<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BrandSeeder extends Seeder
{
    /**
     * Seed the official 22 KCODE Korean skincare brands from V24 Developer Pack.
     */
    public function run(): void
    {
        $brands = [
            ['name_en' => 'ABIB', 'name_ar' => 'أبيب', 'logo' => 'abib.png'],
            ['name_en' => 'ANUA', 'name_ar' => 'أنوا', 'logo' => 'anua.png'],
            ['name_en' => 'Arencia', 'name_ar' => 'أرينسيا', 'logo' => 'arencia.png'],
            ['name_en' => 'AXIS-Y', 'name_ar' => 'أكسيس واي', 'logo' => 'axis-y.png'],
            ['name_en' => 'Beauty of Joseon', 'name_ar' => 'بيوتي أوف جوسون', 'logo' => 'beauty-of-joseon.png'],
            ['name_en' => 'BIODANCE', 'name_ar' => 'بيو دانس', 'logo' => 'biodance.png'],
            ['name_en' => 'celimax', 'name_ar' => 'سيليماكس', 'logo' => 'celimax.png'],
            ['name_en' => 'COSRX', 'name_ar' => 'كوزريكس', 'logo' => 'cosrx.png'],
            ['name_en' => 'Dr.Althea', 'name_ar' => 'د. ألثيا', 'logo' => 'dr-althea.png'],
            ['name_en' => 'Dr.Melaxin', 'name_ar' => 'د. ميلاكسين', 'logo' => 'dr-melaxin.png'],
            ['name_en' => 'Dr.Reju-All', 'name_ar' => 'د. ريجو أول', 'logo' => 'dr-reju-all.png'],
            ['name_en' => 'EQQUALBERRY', 'name_ar' => 'إيكوالبيري', 'logo' => 'eqqualberry.png'],
            ['name_en' => 'ISNTREE', 'name_ar' => 'إزنتري', 'logo' => 'isntree.png'],
            ['name_en' => 'Mary&May', 'name_ar' => 'ماري أند ماي', 'logo' => 'mary-and-may.png'],
            ['name_en' => 'Medicube', 'name_ar' => 'ميديكيوب', 'logo' => 'medicube.png'],
            ['name_en' => 'MIXSOON', 'name_ar' => 'مكسون', 'logo' => 'mixsoon.png'],
            ['name_en' => 'NUMBUZIN', 'name_ar' => 'نمبوزن', 'logo' => 'numbuzin.png'],
            ['name_en' => 'PURITO', 'name_ar' => 'بوريتو', 'logo' => 'purito.png'],
            ['name_en' => 'SKIN1004', 'name_ar' => 'سكِن1004', 'logo' => 'skin1004.png'],
            ['name_en' => 'SOME BY MI', 'name_ar' => 'سام باي مي', 'logo' => 'some-by-mi.png'],
            ['name_en' => 'TORRIDEN', 'name_ar' => 'توريدن', 'logo' => 'torriden.png'],
            ['name_en' => 'VT COSMETICS', 'name_ar' => 'في تي كوزمتكس', 'logo' => 'vt-cosmetics.png'],
        ];

        $sourceDir = 'c:/Users/Dell/Downloads/KCODE_Homepage_Developer_V24_FINAL/KCODE_Homepage_Developer_V24_FINAL/assets/brands';
        $targetDir = storage_path('app/public/brands');

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        $allowedNames = [];

        foreach ($brands as $brand) {
            $allowedNames[] = strtolower($brand['name_en']);

            // Copy logo image if available in assets package
            $srcFile = $sourceDir . '/' . $brand['logo'];
            $destFile = $targetDir . '/' . $brand['logo'];

            if (File::exists($srcFile)) {
                File::copy($srcFile, $destFile);
            }

            // Find existing brand case-insensitively or create new
            $existing = Brand::whereRaw('LOWER(name_en) = ?', [strtolower($brand['name_en'])])->first();

            if ($existing) {
                $existing->update([
                    'name_en' => $brand['name_en'],
                    'name_ar' => $brand['name_ar'],
                    'image'   => $brand['logo'],
                ]);
            } else {
                Brand::create([
                    'name_en' => $brand['name_en'],
                    'name_ar' => $brand['name_ar'],
                    'image'   => $brand['logo'],
                ]);
            }
        }

        // Delete any brand not in the 22 official Developer Pack list cleanly
        Schema::disableForeignKeyConstraints();
        $allBrands = Brand::all();
        foreach ($allBrands as $b) {
            if (!in_array(strtolower($b->name_en), $allowedNames)) {
                \App\Models\Product::where('brand_id', $b->id)->delete();
                $b->delete();
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}
