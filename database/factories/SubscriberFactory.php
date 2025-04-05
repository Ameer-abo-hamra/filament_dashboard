<?php

namespace Database\Factories;

use App\Models\CountryCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CountryCount; // النموذج المرتبط بالدولة
use App\Models\Coin;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'username' => $this->faker->userName,
            'nationality' => $this->faker->country,
            'birthdate' => $this->faker->date(),
            'gender' => $this->faker->randomElement([0, 1, 2]),
            'mobile' => $this->faker->phoneNumber,
            'verification_code' => rand(100000, 999999),
            'is_active' => true, // تفعيل الحساب
            'is_verified' => true, // التحقق من الحساب
            'address' => $this->faker->address,
            'reset_token' => $this->faker->uuid,
            'country_code_id' => 2 , // علاقة مع CountryCount
        ];
    }
}
