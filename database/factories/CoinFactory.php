<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CoinFactory extends Factory
{
    protected $model = \App\Models\Coin::class;

    public function definition()
    {
        $coins = [
            ['name' => 'US Dollar', 'symbol' => '$'],
            ['name' => 'Euro', 'symbol' => '€'],
            ['name' => 'Saudi Riyal', 'symbol' => 'SAR'],
            ['name' => 'UAE Dirham', 'symbol' => 'AED'],
            ['name' => 'Egyptian Pound', 'symbol' => 'EGP'],
            ['name' => 'Syrian Pound', 'symbol' => 'SYP'],
            ['name' => 'Kuwaiti Dinar', 'symbol' => 'KWD'],
            ['name' => 'Jordanian Dinar', 'symbol' => 'JOD'],
            ['name' => 'Moroccan Dirham', 'symbol' => 'MAD'],
            ['name' => 'Algerian Dinar', 'symbol' => 'DZD'],
            ['name' => 'Qatari Riyal', 'symbol' => 'QAR'],
            ['name' => 'Bahraini Dinar', 'symbol' => 'BHD'],
            ['name' => 'Omani Rial', 'symbol' => 'OMR'],
            ['name' => 'Iraqi Dinar', 'symbol' => 'IQD'],
            ['name' => 'Tunisian Dinar', 'symbol' => 'TND'],
        ];

        $coin = $this->faker->randomElement($coins);

        return [
            'name' => $coin['name'],
            'symbol' => $coin['symbol'],
        ];
    }
}
