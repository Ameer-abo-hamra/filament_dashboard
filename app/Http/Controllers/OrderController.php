<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ResponseTrait;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ResponseTrait;
    public function getUserOrders(Request $request)
    {
        $sub = Auth::guard('sub')->user();


        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page_number', 1);
        $sortable_column = $request->input('sortable_column', 'id');
        $sorting_type = $request->input('sorting_type', 'desc');
        $status = $request->input('status');

        $query = Order::query();


        $query->where('sub_id', $sub->id);


        if (!is_null($status)) {
            $query->where('status', $status);
        }


        $query->withCount('items')
            ->orderBy($sortable_column, $sorting_type);

        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        if ($orders->isEmpty()) {
            return $this->returnError('No orders found for this user.', 404);
        }

        return $this->returnData('User orders retrieved successfully.', $orders->getCollection(), 200, $orders);
    }

    public function getOrderItems(Request $request, $orderId)
    {
        $sub = Auth::guard('sub')->user();

        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }

        $order = $sub->orders()->find($orderId);

        if (!$order) {
            return $this->returnError('Order not found or does not belong to the current user.', 404);
        }


        $perPage = $request->input('per_page', 10);
        $page = $request->input('page_number', 1);
        $sortableColumn = $request->input('sortable_column', 'id');
        $sortableType = $request->input('sortable_type', 'asc');





        $query = $order->items();

        if ($request->has('name')) {
            $query->where('order_item.name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('status')) {
            $query->where('order_item.status', $request->input('status'));
        }


        $orderItems = $query->orderBy($sortableColumn, $sortableType)
            ->paginate($perPage, ['*'], 'page', $page);


        $final = $orderItems->getCollection()->transform(function ($i) {
            $i->brand_name = $i->brand->name;
            $i->group_name = $i->group->name;
            $i->category_name = $i->category->name;
            $i->coin_name = $i->coin->name;
            unset($i->brand);
            unset($i->group);
            unset($i->category);
            return $i;
        });

        if ($final->isEmpty()) {
            return $this->returnError('No items found for this order.', 404);
        }

        return $this->returnData('Order items retrieved successfully.', $final, 200, $orderItems);
    }

    public function removeItemFromOrder(Request $request)
    {

        $sub = Auth::guard('sub')->user();
        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }


        $order = $sub->orders()->find($request->input("order_id"));
        if (!$order) {
            return $this->returnError('Order not found or does not belong to the current user.', 404);
        }


        if ($order->status == 1) {
            return $this->returnError('Cannot modify an order with status 1.', 403);
        }


        $itemId = $request->input('item_id');
        $item = $order->items()->where('item_id', $itemId)->first();

        if (!$item) {
            return $this->returnError('Item not found in this order.', 404);
        }


        if ($item->pivot->status == 1) {
            return $this->returnError('Cannot remove item with status 1.', 403);
        }

        DB::beginTransaction();

        try {

            $order->items()->detach($itemId);


            $newTotal = 0;
            foreach ($order->items as $orderItem) {
                $newTotal += $orderItem->pivot->amount * $orderItem->pivot->price;
            }

            $order->total = $newTotal;
            $order->save();


            if ($order->items->isEmpty()) {
                $order->delete();
            }

            DB::commit();

            return $this->returnSuccess('Item removed from order and order updated successfully.', 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->returnError('Error removing item from order: ' . $e->getMessage(), 500);
        }
    }


    public function updateOrderStatus(Request $request)
    {

        $sub = Auth::guard('sub')->user();


        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }


        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|integer|in:0,1'
        ]);


        $order = $sub->orders()->find($validatedData['order_id']);

        if (!$order) {
            return $this->returnError('Order not found or does not belong to the current user.', 404);
        }


        if ($order->status == $validatedData['status']) {
            return $this->returnError('The status is already set to the requested value.', 400);
        }


        $order->status = $validatedData['status'];

        try {
            $order->save();

            return $this->returnSuccess('Order status updated successfully.', 200);
        } catch (\Exception $e) {
            return $this->returnError('Failed to update order status: ' . $e->getMessage(), 500);
        }
    }

    public function updateItemQuantityInOrder(Request $request)
    {

        $sub = Auth::guard('sub')->user();
        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }


        $order = $sub->orders()->find($request->input("order_id"));
        if (!$order) {
            return $this->returnError('Order not found or does not belong to the current user.', 404);
        }


        if ($order->status == 1) {
            return $this->returnError('Cannot modify an order with status 1.', 403);
        }


        $itemId = $request->input('item_id');
        $newQuantity = $request->input('quantity');

        if ($newQuantity <= 0) {
            return $this->returnError('Quantity must be greater than zero.', 400);
        }

        $item = $order->items()->where('item_id', $itemId)->first();

        if (!$item) {
            return $this->returnError('Item not found in this order.', 404);
        }


        if ($item->pivot->status == 1) {
            return $this->returnError('Cannot modify item with status 1.', 403);
        }

        DB::beginTransaction();

        try {

            $item->pivot->amount = $newQuantity;
            $item->pivot->save();


            $newTotal = 0;
            foreach ($order->items as $orderItem) {
                $newTotal += $orderItem->pivot->amount * $orderItem->pivot->price;
            }

            $order->total = $newTotal;
            $order->save();

            DB::commit();

            return $this->returnSuccess('Item quantity updated successfully and order total updated.', 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->returnError('Error updating item quantity in order: ' . $e->getMessage(), 500);
        }
    }

    public function deleteOrder($order_id)
    {

        $sub = Auth::guard('sub')->user();

        if (!$sub) {
            return $this->returnError('User not found.', 404);
        }


        $order = $sub->orders()->with('items')->find($order_id);

        if (!$order) {
            return $this->returnError('Order not found or does not belong to the current user.', 404);
        }


        if ($order->status == 1) {
            return $this->returnError('Cannot delete an order with status 1.', 403);
        }


        $hasItemsWithStatusOne = $order->items->contains(function ($item) {
            return $item->pivot->status == 1;
        });

        if ($hasItemsWithStatusOne) {
            return $this->returnError('Cannot delete an order that contains items with status 1.', 403);
        }


        try {

            $order->items()->detach();


            $order->delete();

            return $this->returnSuccess('Order and its items deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->returnError('Failed to delete order and its items: ' . $e->getMessage(), 500);
        }
    }
}
