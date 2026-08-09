<?php

namespace Database\Seeders;

use App\Models\SalonService;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\WorkingHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalonDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'قصات وعناية', 'slug' => 'cuts-care', 'audience' => 'all', 'sort_order' => 1],
            ['name' => 'ألوان وصبغات', 'slug' => 'colors', 'audience' => 'all', 'sort_order' => 2],
            ['name' => 'أطفال', 'slug' => 'kids', 'audience' => 'kids', 'sort_order' => 3],
        ])->map(fn ($item) => ServiceCategory::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [...$item, 'is_active' => true]
        ));

        $staffMembers = collect([
            ['full_name' => 'Rama Nasser', 'slug' => 'rama-nasser', 'specialty' => 'Cuts & styling', 'experience_years' => 6, 'sort_order' => 1],
            ['full_name' => 'Omar Khaled', 'slug' => 'omar-khaled', 'specialty' => 'Fades & grooming', 'experience_years' => 8, 'sort_order' => 2],
            ['full_name' => 'Lina Youssef', 'slug' => 'lina-youssef', 'specialty' => 'Color specialist', 'experience_years' => 5, 'sort_order' => 3],
        ])->map(fn ($item) => Staff::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [...$item, 'is_active' => true]
        ));

        $services = [
            ['name' => 'قص وتصفيف', 'slug' => 'cut-and-style', 'category' => 'cuts-care', 'audience' => 'all', 'duration' => 45, 'price' => 80, 'featured' => true],
            ['name' => 'حلاقة سريعة للأطفال', 'slug' => 'kids-quick-cut', 'category' => 'kids', 'audience' => 'kids', 'duration' => 30, 'price' => 45, 'featured' => false],
            ['name' => 'صبغة كاملة', 'slug' => 'full-color', 'category' => 'colors', 'audience' => 'all', 'duration' => 120, 'price' => 220, 'featured' => true],
            ['name' => 'غسيل وعناية', 'slug' => 'wash-care', 'category' => 'cuts-care', 'audience' => 'all', 'duration' => 25, 'price' => 35, 'featured' => false],
        ];

        foreach ($services as $index => $item) {
            $category = $categories->firstWhere('slug', $item['category']);

            $service = SalonService::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'service_category_id' => $category->id,
                    'name' => $item['name'],
                    'audience' => $item['audience'],
                    'duration_label' => $item['duration'].' min',
                    'duration_minutes' => $item['duration'],
                    'price' => $item['price'],
                    'description' => 'خدمة احترافية ضمن بيئة مريحة وسريعة الحجز.',
                    'is_featured' => $item['featured'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );

            $service->staff()->syncWithoutDetaching(
                $staffMembers->pluck('id')->take($item['slug'] === 'kids-quick-cut' ? 1 : 2)->all()
            );
        }

        foreach ($staffMembers as $staff) {
            foreach (range(0, 6) as $day) {
                WorkingHour::query()->updateOrCreate(
                    ['staff_id' => $staff->id, 'day_of_week' => $day],
                    [
                        'start_time' => '10:00:00',
                        'end_time' => '20:00:00',
                        'is_day_off' => $day === 5,
                    ]
                );
            }
        }
    }
}
