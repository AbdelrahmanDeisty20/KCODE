<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Adds realistic customer testimonials (general website reviews, product_id = null)
 * for the homepage section «ما يقوله عملاؤنا بعد التجربة».
 *
 * SAFE:
 *  - Never updates or deletes existing reviews/users.
 *  - Idempotent: users matched by email, reviews matched by user (product_id null),
 *    so running it again will NOT create duplicates.
 */
class CustomerTestimonialsSeeder extends Seeder
{
    private const TAGS = [
        'order'   => ['ar' => 'الطلب والتغليف',   'en' => 'Order & Packaging'],
        'service' => ['ar' => 'خدمة العملاء',     'en' => 'Customer Service'],
        'routine' => ['ar' => 'الروتين المقترَح', 'en' => 'Suggested Routine'],
        'kcode'   => ['ar' => 'تجربة KCODE',      'en' => 'KCODE Experience'],
    ];

    public function run(): void
    {
        $testimonials = [
            // ── Featured (homepage) ─────────────────────────────
            [
                'name' => 'مريم البلوشية', 'city' => 'مسقط', 'tag' => 'order', 'rating' => 5, 'featured' => true, 'days' => 1,
                'comment' => 'الطلب وصلني خلال يوم واحد والتغليف كان أنيق جداً ومحكم، حسّيت إني فاتحة هدية مش طلب أونلاين. المنتجات أصلية وتواريخها حديثة.',
            ],
            [
                'name' => 'سالم الحارثي', 'city' => 'السيب', 'tag' => 'service', 'rating' => 5, 'featured' => true, 'days' => 2,
                'comment' => 'تواصلت مع خدمة العملاء عشان أسأل عن منتج مناسب لبشرة زوجتي الحساسة، الرد كان سريع وفيه اهتمام حقيقي بالتفاصيل. تعامل راقي.',
            ],
            [
                'name' => 'نوف الهنائية', 'city' => 'نزوى', 'tag' => 'routine', 'rating' => 5, 'featured' => true, 'days' => 3,
                'comment' => 'جربت الروتين المقترح بعد اختبار البشرة، وبعد ثلاث أسابيع لاحظت فرق واضح في الحبوب والمسام. أخيراً روتين بسيط ومفهوم وفعّال.',
            ],
            [
                'name' => 'خلود المعمرية', 'city' => 'صحار', 'tag' => 'kcode', 'rating' => 5, 'featured' => true, 'days' => 4,
                'comment' => 'تجربة KCODE من أحلى تجارب التسوق اللي مرت علي، الموقع سهل والشرح لكل منتج واضح، وصرت أعرف بالضبط إيش يناسب بشرتي.',
            ],

            // ── Extra (paginated list) ──────────────────────────
            [
                'name' => 'أحمد الرواحي', 'city' => 'بوشر', 'tag' => 'order', 'rating' => 5, 'featured' => false, 'days' => 6,
                'comment' => 'ثاني طلب لي من الموقع، نفس الجودة ونفس سرعة التوصيل. الكمية كانت مضبوطة ومافي أي منتج متضرر.',
            ],
            [
                'name' => 'شيخة الكندية', 'city' => 'صلالة', 'tag' => 'service', 'rating' => 4, 'featured' => false, 'days' => 8,
                'comment' => 'كان عندي استفسار بخصوص الاستبدال وتم حله بسهولة ومن غير تعقيد. التوصيل لصلالة أخذ يومين بس كان يستاهل الانتظار.',
            ],
            [
                'name' => 'عائشة الشكيلية', 'city' => 'العامرات', 'tag' => 'routine', 'rating' => 5, 'featured' => false, 'days' => 10,
                'comment' => 'بشرتي جافة جداً وكنت محتارة، الروتين المقترح كان متوازن ومش مكلف، والترطيب صار يدوم معي طول اليوم.',
            ],
            [
                'name' => 'يوسف العبري', 'city' => 'صور', 'tag' => 'kcode', 'rating' => 5, 'featured' => false, 'days' => 12,
                'comment' => 'اشتريت هدية لأختي من KCODE وعجبتها جداً، والبطاقة المرفقة مع الطلب كانت لمسة حلوة. أكيد بكرر التجربة.',
            ],
            [
                'name' => 'ريم الزدجالية', 'city' => 'مسقط', 'tag' => 'order', 'rating' => 4, 'featured' => false, 'days' => 14,
                'comment' => 'التغليف ممتاز والمنتجات أصلية، بس تمنيت يكون فيه خيار توصيل بنفس اليوم. غير كذا كل شي كان رائع.',
            ],
            [
                'name' => 'حمد السعدي', 'city' => 'الرستاق', 'tag' => 'service', 'rating' => 5, 'featured' => false, 'days' => 16,
                'comment' => 'فريق الدعم ساعدني أختار واقي شمس مناسب للبشرة الدهنية، وفعلاً ما يسبب لمعة ولا حبوب. شكراً على النصيحة الصادقة.',
            ],
            [
                'name' => 'منى الغافرية', 'city' => 'عبري', 'tag' => 'routine', 'rating' => 5, 'featured' => false, 'days' => 18,
                'comment' => 'أول مرة ألتزم بروتين كامل لمدة شهر، والسبب إنه واضح ومرتب خطوة بخطوة. البقع الداكنة خفّت بشكل ملحوظ.',
            ],
            [
                'name' => 'بدر الفارسي', 'city' => 'صحار', 'tag' => 'kcode', 'rating' => 4, 'featured' => false, 'days' => 20,
                'comment' => 'تشكيلة المنتجات الكورية ممتازة والأسعار مناسبة مقارنة بالسوق. تمنيت لو فيه برنامج نقاط للعملاء الدائمين.',
            ],
        ];

        $created = 0;

        foreach ($testimonials as $i => $t) {
            $user = User::firstOrCreate(
                ['email' => 'testimonial' . ($i + 1) . '@kcode.om'],
                [
                    'name'     => $t['name'],
                    'password' => Hash::make(str()->random(32)),
                    'city'     => $t['city'],
                    'type'     => 'user',
                    'status'   => 'active',
                ]
            );

            // Fill city only if empty — never overwrite
            if (blank($user->city)) {
                $user->update(['city' => $t['city']]);
            }

            // One general review per seeded user — skip if already exists
            $exists = Review::where('user_id', $user->id)->whereNull('product_id')->exists();
            if ($exists) {
                continue;
            }

            $date = now()->subDays($t['days']);

            $review = new Review([
                'product_id' => null,
                'user_id'    => $user->id,
                'rating'     => $t['rating'],
                'comment'    => $t['comment'],
                'tag_ar'     => self::TAGS[$t['tag']]['ar'],
                'tag_en'     => self::TAGS[$t['tag']]['en'],
                'featured'   => $t['featured'],
            ]);
            $review->created_at = $date;
            $review->updated_at = $date;
            $review->save();

            $created++;
        }

        $this->command?->info("CustomerTestimonialsSeeder: {$created} new testimonials added.");
    }
}
