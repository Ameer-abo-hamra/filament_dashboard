<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Traits\ResponseTrait;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseTrait;

    public function addItemToCart(Request $request)
    {
        $subscriber = auth("sub")->user();
        $cart = $subscriber->cart;

        if (!$cart) {
            return $this->returnError('You do not have a cart yet. Please create a cart first.');
        }

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        $item = Item::find($request->item_id);

        if ($item->amount < $request->amount) {
            return $this->returnError('Not enough stock available for this item.');
        }

        if ($request->amount < $item->Minimum_sale) {
            return $this->returnError('The minimum quantity required for this item is ' . $item->Minimum_sale . ' units.');
        }

        $existingItem = $cart->items()->where('item_id', $item->id)->first();
        if ($existingItem) {
            return $this->returnError('This item is already in your cart. you can update the amount if you want :)');
        }

        $cart->items()->attach($item->id, [
            'check_out' => false,
            'price' => $item->price,
            "amount" => $request->amount,
            "Minimum_sale"=>$item->Minimum_sale
        ]);

        $this->calculateCartTotal($cart);

        return $this->returnSuccess('Item added to cart successfully.');
    }

    public function removeItemFromCart(Request $request)
    {

        $sub = Auth::guard('sub')->user();


        $cart = $sub->cart;


        if (!$cart) {
            return $this->returnError('No cart found for the current user.', 404);
        }


        $itemId = $request->input('item_id');


        $item = $cart->items()->where('item_id', $itemId)->first();

        if (!$item) {
            return $this->returnError('Item not found in cart.', 404);
        }


        DB::beginTransaction();

        try {

            $orders = $cart->orders;



            $cart->items()->detach($itemId);


            foreach ($orders as $order) {
                $order->items()->detach($itemId);
            }


            $this->calculateCartTotal($cart);


            foreach ($orders as $order) {
                $orderTotal = 0;
                foreach ($order->items as $orderItem) {
                    $orderTotal += $orderItem->pivot->amount * $orderItem->pivot->price;
                }


                $order->total = $orderTotal;
                $order->save();


                if ($order->items->isEmpty()) {

                    $order->delete();
                }
            }


            DB::commit();

            return $this->returnSuccess('Item removed from cart and orders updated successfully.', 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->returnError('Error removing item from cart: ' . $e->getMessage(), 500);
        }
    }



    public function updateItemQuantityInCart(Request $request)
    {
        $subscriber = auth("sub")->user();
        $cart = $subscriber->cart;


        if (!$cart) {
            return $this->returnError('You do not have a cart yet. Please create a cart first.');
        }


        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }


        $item = Item::find($request->item_id);

        $cartItem = $cart->items()->where('item_id', $item->id)->first();


        if (!$cartItem) {
            return $this->returnError('Item not found in your cart.');
        }

        if ($item->amount < $request->amount) {
            return $this->returnError('Not enough stock available for this item.');
        }

        if ($request->amount < $item->Minimum_sale) {
            return $this->returnError('Quantity must be at least ' . $item->Minimum_sale . ' for this item.');
        }

        $cart->items()->updateExistingPivot($item->id, [
            'amount' => $request->amount,
        ]);


        $this->calculateCartTotal($cart);

        return $this->returnSuccess('Item quantity updated successfully.');
    }

    public function checkOut(Request $request)
    {
        $sub = Auth::guard('sub')->user();
        $cart = $sub->cart;

        if (!$cart) {
            return $this->returnError('No cart found for the current user.', 404);
        }

        if ($cart->items()->count() > 0) {
            $itemsToCheckout = $cart->items()->wherePivot('check_out', 0)->get();

            if ($itemsToCheckout->isEmpty()) {
                return $this->returnError('No items to check out.', 400);
            }

            DB::beginTransaction();

            try {
                $cart->items()->updateExistingPivot(
                    $itemsToCheckout->pluck('id')->toArray(),
                    ['check_out' => true]
                );

                $cart->update(['check_out_date' => now()]);

                $order = Order::create([
                    'cart_id' => $cart->id,
                    'sub_id' => $sub->id,
                    'total' => 0,
                    'order_date' => now(),
                    'status' => 0,
                ]);

                foreach ($itemsToCheckout as $item) {
                    $amount = $item->pivot->amount ?? 1;

                    if ($amount > 0) {
                        $order->items()->attach($item->id, [
                            'amount' => $amount,
                            'price' => $item->price,
                            "created_at" => now(),
                            "updated_at" => now(),
                            "name" => $item->name
                        ]);
                    } else {
                        return $this->returnError('Invalid quantity for item ' . $item->name, 400);
                    }
                }

                $this->calculateOrderTotal($order);


                $cart->items()->detach($itemsToCheckout->pluck('id')->toArray());
                $this->calculateCartTotal($cart);
                DB::commit();

                return $this->returnSuccess('Check-out and order created successfully. Items removed from cart.', 200);
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->returnError('Error during check-out: ' . $e->getMessage(), 500);
            }
        }

        return $this->returnError('No items in cart to check out.', 400);
    }

    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->pivot->amount * ($item->price + $item->commission);
        }
        $cart->total = $total;
        $cart->save();
        return $total;
    }

    private function calculateOrderTotal($order)
    {
        $total = 0;
        foreach ($order->items as $item) {
            $total += $item->pivot->amount * ($item->pivot->price + $item->commission);
        }
        $order->total = $total;
        $order->save();
        return $total;
    }

    public function getCartWithItems(Request $request)
    {
        $sub = Auth::guard('sub')->user();


        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }


        $cart = $sub->cart;

        if (!$cart) {
            return $this->returnError('Cart not found for the current user.', 404);
        }


        $perPage = $request->input('per_page', 10);
        $page = $request->input('page_number', 1);


        $cartItems = $cart->items()->paginate($perPage, ['*'], 'page', $page);


        $transformedItems = $cartItems->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->pivot->amount,
                'brand_name' => $item->brand->name ?? null,
                'category_name' => $item->category->name ?? null,
                'group_name' => $item->group->name ?? null,
                "coin" => $item->coin ?? null,
                "commission"  => $item->commission ?? null,
                "description" => $item->description  ?? null,
                "amount" => $item->amount ?? null,
                "pivot" => $item->pivot ?? null,
                "image" => $item->image ?? null,
            ];
        });


        $result = [
            'cart' => [
                'id' => $cart->id,
                'total' => $cart->total,
                'created_at' => $cart->created_at,
                'updated_at' => $cart->updated_at,
            ],
            'items' => $transformedItems,

        ];

        return $this->returnData('Cart and items retrieved successfully.', $result, 200, $cartItems);
    }
}
