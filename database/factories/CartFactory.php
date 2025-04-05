<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Ensure sub_id is sequential from 1 to 10
        static $subIdCounter = 1;

        // If the counter exceeds 10, stop creating carts
        if ($subIdCounter > 10) {
            return null;
        }

        // Get the subscriber with the sub_id matching the counter (ensure sequential sub_id)
        $subscriber = Subscriber::find($subIdCounter);

        // Increment the counter for next cart
        $subIdCounter++;

        // التحقق إذا كان للمستخدم سلة بالفعل
        $existingCart = Cart::where('sub_id', $subscriber->id)->first();

        // إذا كانت السلة موجودة، لن نعيد إنشاء سلة جديدة
        if ($existingCart) {
            return [
                'sub_id' => $existingCart->sub_id,
                'total' => $existingCart->total,
                'check_out_date' => $existingCart->check_out_date,
                'created_at' => $existingCart->created_at,
                'updated_at' => $existingCart->updated_at,
            ];
        }

        // إنشاء سلة جديدة مع sub_id من 1 إلى 10
        return [
            'sub_id' => $subscriber->id, // Use sequential sub_id
            'total' =>0,
            'check_out_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
