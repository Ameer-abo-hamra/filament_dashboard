<?php
namespace Database\Factories;

use App\Models\CountryCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryCodeFactory extends Factory
{
    protected $model = CountryCode::class;

    public function definition()
    {
        $countries = [
            ['country_name' => 'Syria', 'country_code' => '+963', 'iso_code' => 'SY', 'region' => 'Middle East'],
            ['country_name' => 'United Arab Emirates', 'country_code' => '+971', 'iso_code' => 'AE', 'region' => 'Middle East'],
            ['country_name' => 'Saudi Arabia', 'country_code' => '+966', 'iso_code' => 'SA', 'region' => 'Middle East'],
            ['country_name' => 'Egypt', 'country_code' => '+20', 'iso_code' => 'EG', 'region' => 'Africa'],
            ['country_name' => 'Jordan', 'country_code' => '+962', 'iso_code' => 'JO', 'region' => 'Middle East'],
            ['country_name' => 'Lebanon', 'country_code' => '+961', 'iso_code' => 'LB', 'region' => 'Middle East'],
            ['country_name' => 'Iraq', 'country_code' => '+964', 'iso_code' => 'IQ', 'region' => 'Middle East'],
            ['country_name' => 'Kuwait', 'country_code' => '+965', 'iso_code' => 'KW', 'region' => 'Middle East'],
            ['country_name' => 'Qatar', 'country_code' => '+974', 'iso_code' => 'QA', 'region' => 'Middle East'],
            ['country_name' => 'Bahrain', 'country_code' => '+973', 'iso_code' => 'BH', 'region' => 'Middle East'],
            ['country_name' => 'Oman', 'country_code' => '+968', 'iso_code' => 'OM', 'region' => 'Middle East'],
            ['country_name' => 'Morocco', 'country_code' => '+212', 'iso_code' => 'MA', 'region' => 'Africa'],
            ['country_name' => 'Tunisia', 'country_code' => '+216', 'iso_code' => 'TN', 'region' => 'Africa'],
            ['country_name' => 'Algeria', 'country_code' => '+213', 'iso_code' => 'DZ', 'region' => 'Africa'],
            ['country_name' => 'Libya', 'country_code' => '+218', 'iso_code' => 'LY', 'region' => 'Africa'],
            ['country_name' => 'Sudan', 'country_code' => '+249', 'iso_code' => 'SD', 'region' => 'Africa'],
            ['country_name' => 'Palestine', 'country_code' => '+970', 'iso_code' => 'PS', 'region' => 'Middle East'],
            ['country_name' => 'Yemen', 'country_code' => '+967', 'iso_code' => 'YE', 'region' => 'Middle East'],
        ];

        // الحصول على الدول بشكل منتظم واحد تلو الآخر
        static $index = 0;  // لتتبع أي دولة يتم إدخالها
        if ($index >= count($countries)) {
            $index = 0;  // إعادة البدء إذا تم الوصول إلى آخر دولة
        }

        $country = $countries[$index];
        $index++;  // الانتقال إلى الدولة التالية

        return [
            'country_name' => $country['country_name'],
            'country_code' => $country['country_code'],
            'iso_code' => $country['iso_code'],
            'region' => $country['region'],
        ];
    }
}
