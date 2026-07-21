<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cities')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $countriesData = [
            [
                'country' => ['name_ar' => 'سلطنة عمان', 'name_en' => 'Sultanate of Oman'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'مسقط', 'name_en' => 'Muscat'],
                        'cities' => [
                            ['name_ar' => 'السيب', 'name_en' => 'Seeb'],
                            ['name_ar' => 'بوشر', 'name_en' => 'Bawshar'],
                            ['name_ar' => 'مطرح', 'name_en' => 'Mutrah'],
                            ['name_ar' => 'العامرات', 'name_en' => 'Al Amrat'],
                            ['name_ar' => 'مسقط', 'name_en' => 'Muscat'],
                            ['name_ar' => 'قريات', 'name_en' => 'Qurayyat'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'ظفار', 'name_en' => 'Dhofar'],
                        'cities' => [
                            ['name_ar' => 'صلالة', 'name_en' => 'Salalah'],
                            ['name_ar' => 'طاقة', 'name_en' => 'Taqah'],
                            ['name_ar' => 'مرباط', 'name_en' => 'Mirbat'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الداخلية', 'name_en' => 'Ad Dakhiliyah'],
                        'cities' => [
                            ['name_ar' => 'نزوى', 'name_en' => 'Nizwa'],
                            ['name_ar' => 'بهلاء', 'name_en' => 'Bahla'],
                            ['name_ar' => 'سمائل', 'name_en' => 'Samail'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'شمال الباطنة', 'name_en' => 'Al Batinah North'],
                        'cities' => [
                            ['name_ar' => 'صحار', 'name_en' => 'Sohar'],
                            ['name_ar' => 'الخابورة', 'name_en' => 'Al Khaburah'],
                            ['name_ar' => 'السويق', 'name_en' => 'Al Suwaiq'],
                            ['name_ar' => 'شناص', 'name_en' => 'Shinas'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'جنوب الباطنة', 'name_en' => 'Al Batinah South'],
                        'cities' => [
                            ['name_ar' => 'الرستاق', 'name_en' => 'Rustaq'],
                            ['name_ar' => 'بركاء', 'name_en' => 'Barka'],
                            ['name_ar' => 'المصنعة', 'name_en' => 'Al Musannah'],
                        ]
                    ],
                ]
            ],
            [
                'country' => ['name_ar' => 'المملكة العربية السعودية', 'name_en' => 'Kingdom of Saudi Arabia'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'الرياض', 'name_en' => 'Riyadh'],
                        'cities' => [
                            ['name_ar' => 'الرياض', 'name_en' => 'Riyadh'],
                            ['name_ar' => 'الخرج', 'name_en' => 'Al-Kharj'],
                            ['name_ar' => 'الدرعية', 'name_en' => 'Ad-Diriyah'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'مكة المكرمة', 'name_en' => 'Makkah'],
                        'cities' => [
                            ['name_ar' => 'مكة المكرمة', 'name_en' => 'Makkah'],
                            ['name_ar' => 'جدة', 'name_en' => 'Jeddah'],
                            ['name_ar' => 'الطائف', 'name_en' => 'Taif'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'المنطقة الشرقية', 'name_en' => 'Eastern Province'],
                        'cities' => [
                            ['name_ar' => 'الدمام', 'name_en' => 'Dammam'],
                            ['name_ar' => 'الخبر', 'name_en' => 'Khobar'],
                            ['name_ar' => 'الجبيل', 'name_en' => 'Jubail'],
                            ['name_ar' => 'الأحساء', 'name_en' => 'Al-Ahsa'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'المدينة المنورة', 'name_en' => 'Medina'],
                        'cities' => [
                            ['name_ar' => 'المدينة المنورة', 'name_en' => 'Medina'],
                            ['name_ar' => 'ينبع', 'name_en' => 'Yanbu'],
                        ]
                    ]
                ]
            ],
            [
                'country' => ['name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'أبوظبي', 'name_en' => 'Abu Dhabi'],
                        'cities' => [
                            ['name_ar' => 'أبوظبي', 'name_en' => 'Abu Dhabi'],
                            ['name_ar' => 'العين', 'name_en' => 'Al Ain'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'دبي', 'name_en' => 'Dubai'],
                        'cities' => [
                            ['name_ar' => 'دبي', 'name_en' => 'Dubai'],
                            ['name_ar' => 'حتا', 'name_en' => 'Hatta'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الشارقة', 'name_en' => 'Sharjah'],
                        'cities' => [
                            ['name_ar' => 'الشارقة', 'name_en' => 'Sharjah'],
                            ['name_ar' => 'خورفكان', 'name_en' => 'Khor Fakkan'],
                            ['name_ar' => 'كلباء', 'name_en' => 'Kalba'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'عجمان', 'name_en' => 'Ajman'],
                        'cities' => [
                            ['name_ar' => 'عجمان', 'name_en' => 'Ajman'],
                        ]
                    ]
                ]
            ],
            [
                'country' => ['name_ar' => 'دولة قطر', 'name_en' => 'State of Qatar'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'الدوحة', 'name_en' => 'Doha'],
                        'cities' => [
                            ['name_ar' => 'الدوحة', 'name_en' => 'Doha'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الريان', 'name_en' => 'Al Rayyan'],
                        'cities' => [
                            ['name_ar' => 'الريان', 'name_en' => 'Al Rayyan'],
                            ['name_ar' => 'معيذر', 'name_en' => 'Muaither'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الخور', 'name_en' => 'Al Khor'],
                        'cities' => [
                            ['name_ar' => 'الخور', 'name_en' => 'Al Khor'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الوكرة', 'name_en' => 'Al Wakrah'],
                        'cities' => [
                            ['name_ar' => 'الوكرة', 'name_en' => 'Al Wakrah'],
                        ]
                    ]
                ]
            ],
            [
                'country' => ['name_ar' => 'دولة الكويت', 'name_en' => 'State of Kuwait'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'العاصمة', 'name_en' => 'Al Asimah'],
                        'cities' => [
                            ['name_ar' => 'الكويت', 'name_en' => 'Kuwait City'],
                            ['name_ar' => 'اليرموك', 'name_en' => 'Yarmouk'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'حولي', 'name_en' => 'Hawally'],
                        'cities' => [
                            ['name_ar' => 'حولي', 'name_en' => 'Hawally'],
                            ['name_ar' => 'السالمية', 'name_en' => 'Salmiya'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الأحمدي', 'name_en' => 'Al Ahmadi'],
                        'cities' => [
                            ['name_ar' => 'الأحمدي', 'name_en' => 'Al Ahmadi'],
                            ['name_ar' => 'الفحيحيل', 'name_en' => 'Fahaheel'],
                        ]
                    ]
                ]
            ],
            [
                'country' => ['name_ar' => 'مملكة البحرين', 'name_en' => 'Kingdom of Bahrain'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'العاصمة', 'name_en' => 'Capital'],
                        'cities' => [
                            ['name_ar' => 'المنامة', 'name_en' => 'Manama'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'المحرق', 'name_en' => 'Muharraq'],
                        'cities' => [
                            ['name_ar' => 'المحرق', 'name_en' => 'Muharraq'],
                            ['name_ar' => 'البسيتين', 'name_en' => 'Busaiteen'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الشمالية', 'name_en' => 'Northern'],
                        'cities' => [
                            ['name_ar' => 'البديع', 'name_en' => 'Budaiya'],
                            ['name_ar' => 'مدينة حمد', 'name_en' => 'Hamad Town'],
                        ]
                    ]
                ]
            ],
            [
                'country' => ['name_ar' => 'جمهورية مصر العربية', 'name_en' => 'Arab Republic of Egypt'],
                'states' => [
                    [
                        'state' => ['name_ar' => 'القاهرة', 'name_en' => 'Cairo'],
                        'cities' => [
                            ['name_ar' => 'القاهرة', 'name_en' => 'Cairo City'],
                            ['name_ar' => 'حلوان', 'name_en' => 'Helwan'],
                            ['name_ar' => 'المعادي', 'name_en' => 'Maadi'],
                            ['name_ar' => 'مصر الجديدة', 'name_en' => 'Heliopolis'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الجيزة', 'name_en' => 'Giza'],
                        'cities' => [
                            ['name_ar' => 'الجيزة', 'name_en' => 'Giza City'],
                            ['name_ar' => '6 أكتوبر', 'name_en' => '6th of October'],
                            ['name_ar' => 'المهندسين', 'name_en' => 'Mohandessin'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria'],
                        'cities' => [
                            ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria City'],
                            ['name_ar' => 'برج العرب', 'name_en' => 'Borg El Arab'],
                        ]
                    ],
                    [
                        'state' => ['name_ar' => 'القليوبية', 'name_en' => 'Qalyubia'],
                        'cities' => [
                            ['name_ar' => 'بنها', 'name_en' => 'Banha'],
                            ['name_ar' => 'شبرا الخيمة', 'name_en' => 'Shubra El-Kheima'],
                        ]
                    ]
                ]
            ],
        ];

        foreach ($countriesData as $cData) {
            $countryId = DB::table('countries')->insertGetId([
                'name_ar'    => $cData['country']['name_ar'],
                'name_en'    => $cData['country']['name_en'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cData['states'] as $sData) {
                $stateId = DB::table('states')->insertGetId([
                    'country_id' => $countryId,
                    'name_ar'    => $sData['state']['name_ar'],
                    'name_en'    => $sData['state']['name_en'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($sData['cities'] as $city) {
                    DB::table('cities')->insert([
                        'state_id'   => $stateId,
                        'country_id' => $countryId,
                        'name_ar'    => $city['name_ar'],
                        'name_en'    => $city['name_en'],
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
