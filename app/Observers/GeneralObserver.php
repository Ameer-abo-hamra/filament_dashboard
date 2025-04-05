<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;

class GeneralObserver
{
    public function creating(Model $model)
    {
        if (method_exists($model, 'inserted_by')) {
            if (Auth::guard("admin")->check()) {
                $model->inserted_by = Auth::guard("admin")->user()->id;
            }
        }
    }

    public function created(Model $model)
    {
        if ($model instanceof Item) {
            $this->checkGroupDeletable($model);
        }
    }

    public function updating(Model $model)
    {
        if (method_exists($model, 'updated_by')) {
            if (Auth::guard("admin")->check()) {

            }
        }
    }

    public function updated(Model $model)
    {

        if ($model instanceof Item) {
            $this->checkGroupDeletable($model);
        }

        if ($model instanceof Order) {
            $this->checkOrderStatus($model);
        }
        if (Auth::guard("admin")->check()) {
            $model->updated_by = Auth::guard("admin")->user()->id;
            $model->saveQuietly();
        }
    }

    public function deleting(Model $model)
    {
        if (method_exists($model, 'deleted_by')) {
            if (Auth::guard("admin")->check()) {
                $model->deleted_by = Auth::guard("admin")->user()->id;
                $model->saveQuietly();
            }
        }
    }

    private function checkGroupDeletable(Item $item)
    {
        $group = $item->group;

        if ($group) {
            $hasActiveItems = $group->items()->where('status', 1)->exists();

            $group->deletable = !$hasActiveItems;

            if ($group->isDirty('deletable')) {
                $group->save();
            }
        }
    }

    private function checkOrderStatus(Order $order)
    {

        if ($order->status == 1) {
            $order->deletable = 0;
            $order->editable = 0;
        } else {
            $order->deletable = 1;
            $order->editable = 1;
        }


        if ($order->isDirty(['deletable', 'editable'])) {
            $order->saveQuietly();
        }
    }

}
