<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Coin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'group_id' => Group::query()->inRandomOrder()->value('id'), // اختيار عشوائي من المجموعات
            'category_id' => rand(1, 5), // عشوائي بين 1 و 5
            'brand_id' => rand(1, 5), // عشوائي بين 1 و 5
            'coin_id' => Coin::query()->inRandomOrder()->value('id'), // اختيار عشوائي من العملات
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->text(200),
            'is_new' => $this->faker->boolean(80), // احتمال 80% أن يكون جديدًا
            'price' => $this->faker->randomFloat(2, 5, 1000), // سعر بين 5 و 1000
            'amount' => $this->faker->numberBetween(10, 500), // كمية بين 10 و 500
            'total' => fn(array $attributes) => $attributes['price'] * $attributes['amount'], // حساب المجموع تلقائيًا
            'status' => $this->faker->randomElement([0, 1]), // 0 أو 1
            'Reason_rejection' => $this->faker->optional(0.2)->sentence, // سبب الرفض (احتمالية 20% أن يظهر)
            'star' => $this->faker->randomElement([1, 0]),
            'sold' => $this->faker->numberBetween(0, 127), // العناصر المباعة
            'editable' => $this->faker->boolean(90), // احتمال 90% أن يكون قابلًا للتعديل
            'locked' => $this->faker->boolean(10), // احتمال 10% أن يكون مقفلًا
            'visitors' => $this->faker->numberBetween(0, 5000), // عدد الزوار
            'image' => $this->faker->imageUrl(640, 480, 'product', true, 'Faker'), // صورة عشوائية
            'Minimum_sale' => $this->faker->numberBetween(1, 100) // الحد الأدنى للبيع
        ];
    }
}
