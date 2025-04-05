<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
class ItemController extends Controller
{
    use ResponseTrait;

    public function addItem(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'group_id' => 'required|exists:groups,id',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_new' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'amount' => 'nullable|integer|min:0',
            'total' => 'nullable|numeric|min:0',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            "minimum_sale" => "required|numeric|min:1|max:100",
            "coin_id" => "required|exists:coins,id"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        if ($request->amount < $request->minimum_sale) {
            return $this->returnError("amount smaller than Minimum sale !");
        }
        try {
            $item = Item::create([

                'group_id' => $request->group_id,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'name' => $request->name,
                'description' => $request->description,
                'is_new' => $request->is_new,
                'price' => $request->price,
                'amount' => $request->amount,
                'total' => $request->total,
                'image' => photo($request, "item", "ItemPhoto"),
                "Minimum_sale" => $request->minimum_sale,
                "coin_id" => $request->coin_id
            ]);

            return $this->returnData("your item is created successfully :)", $item);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function deleteItem($id)
    {

        try {
            $sub = Auth::guard("sub")->user();
            $item = Item::findOrFail($id);
            $group = $item->group;

            if ($group->sub->id == $sub->id) {
                if ($item->status == 0) {
                    $item->delete();
                    return $this->returnSuccess("this item deleted successfully :)");
                } else {
                    return $this->returnError("you cant delete this item any more  :(");

                }
            } else {
                return $this->returnError("you dont have permission to delete this item :(");
            }
        } catch (Exception $e) {
            return $this->returnError("try again later :(");
        }
    }

    public function updateItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,id',
            'brand_id' => 'exists:brands,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'is_new' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'amount' => 'nullable|integer|min:0',
            'total' => 'nullable|numeric|min:0',
            "Minimum_sale" => "nullable|digits|min:1|max:100",
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $sub = Auth::guard("sub")->user();
            $item = Item::findOrFail($id);
            $group = $item->group;

            if ($group->sub->id == $sub->id) {
                if ($item->status == 0) {
                    // تحقق من أن قيمة amount أكبر من أو تساوي Minimum_sale
                    if (
                        $request->has('amount') &&
                        $request->has('minimum_sale') &&
                        $request->amount < $request->minimum_sale
                    ) {
                        return $this->returnError("The amount cannot be less than the Minimum_sale value.");
                    }

                    try {
                        $item->update($request->all());
                    } catch (Exception $e) {
                        return $this->returnError($e->getMessage());
                    }

                    return $this->returnSuccess("This item updated successfully :)");
                } else {
                    return $this->returnError("You can't update this item anymore :(");
                }
            } else {
                return $this->returnError("You don't have permission to update this item :(");
            }
        } catch (Exception $e) {
            return $this->returnError("Try again later :(");
        }
    }


    public function updateItemImage(Request $request, $id)
    {
        try {
            $sub = Auth::guard("sub")->user();
            $item = Item::findOrFail($id);
            $old_path = $item->image;
            $group = $item->group;
            if ($group->sub->id == $sub->id) {
                if ($item->status == 0) {

                    $item->update([
                        'image' => updatePhoto($request, "item", "ItemPhoto", $old_path)
                    ]);
                    return $this->returnSuccess("this item updated successfully :)");
                } else {
                    return $this->returnError("you cant update this item any more  :(");

                }

            }
            return $this->returnError(msgErorr: "you dont have permission to update this item :(");

        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getItemById($id, $group_id)
    {
        try {
            $sub = Auth::guard("sub")->user();
            $item = $sub->groups()->findOrFail($group_id)->items()->findOrFail($id);

            $item->visitors++;
            $item->save();
            $item->group_name = $item->group->name;
            $item->category_name = $item->category->name;
            $item->brand_name = $item->brand->name;
            $item->coin_name = $item->coin->name;
            if ($item->status == 0) {
                $item->deletable = 0;
            } else {
                $item->deletable = 1;
            }
            unset($item->group);
            unset($item->category);
            unset($item->brand);
            return $this->returnData("item ", $item);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getItemByIdHome($id)
    {
        try {
            $item = Item::where("amount", ">", 0)->where("status", 1)->findOrFail($id);
            $item->visitors++;
            $item->save();
            $item->deletable = 1;
            $item->group_name = $item->group->name;
            $item->category_name = $item->category->name;
            $item->brand_name = $item->brand->name;
            $item->coin_name = $item->coin->name;
            unset($item->group);
            unset($item->category);
            unset($item->brand);

            return $this->returnData("item ", $item);

        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
    public function getItemsByVisitors($group_id, $per_page, $page_number, $sort_type = "asc")
    {
        try {
            $sub = auth::guard("sub")->user();

            $items = $sub->groups()
                ->findOrFail($group_id)
                ->first()
                ->items()
                ->orderBy("visitors", $sort_type)
                ->paginate($per_page, ["*"], "page", $page_number);

            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;

                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });

            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getItemsByVisitorsHome(Request $request)
    {
        $per_page = $request->query("per_page", 10);
        $page_number = $request->query("page_number", 1);
        $sorting_type = $request->query("sorting_type", "desc");
        try {
            $items = Item::where('amount', '>', 0)
                ->where('status', 1)
                ->orderBy("visitors", $sorting_type)
                ->paginate($per_page, ["*"], "page", $page_number);

            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                $item->deletable = 1;
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });

            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }



    }

    public function getItemsByIsNew($group_id, $per_page, $page_number, $is_new = 1)
    {
        try {
            $sub = auth::guard("sub")->user();
            $items = $sub->groups()->where("id", $group_id)
                ->first()->items()
                ->where("is_new", $is_new)
                ->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;

                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function searchByName($group_id, $name, $per_page, $page_number)
    {
        try {
            $sub = auth::guard("sub")->user();
            $items = $sub->groups()->findOrFail($group_id)
                ->first()->items()
                ->where('name', 'like', '%' . $name . '%')
                ->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
    /********************** */

    public function getByIdGroup($group_id, $per_page, $page_number)
    {
        try {
            $sub = auth::guard("sub")->user();
            $items = $sub->groups()->findOrFail($group_id)
                ->first()->items()
                ->where('group_id', $group_id)
                ->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);
                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }




    /*************************** */
    public function getByStatus($group_id, $status, $per_page, $page_number)
    {
        try {
            $sub = auth::guard("sub")->user();
            $items = $sub->groups()->findOrFail($group_id)
                ->first()->items()
                ->where('status', $status)
                ->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);
                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    public function getStarItems($group_id, $per_page = 10, $page_number = 1)
    {

        try {
            $sub = Auth::guard("sub")->user();
            $group = $sub->groups()->findOrFail($group_id);

            $items = $group->items()->where("star", "!=", 0)->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name;
                $item->category_name = $item->category->name;
                $item->brand_name = $item->brand->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->group);
                unset($item->category);
                unset($item->brand);
                return $item;
            });

            $group = [
                "group" => $group,
                "items" => $items->getCollection()
            ];

            return $this->returnData("these are items with stars : ", $group, 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getItemsByRane($min, $max, $per_page = 10, $page_number = 1, $sorting_type = "desc")
    {

        try {
            $min = (float) $min;
            $max = (float) $max;
            $per_page = (int) $per_page;
            $page_number = (int) $page_number;

            $items = Item::whereBetween('price', [$min, $max])
                ->where('amount', '>', 0)
                ->where('status', 1)
                ->orderBy('price', $sorting_type)
                ->paginate($per_page, ['*'], 'page', $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand->name;
                $item->category_name = $item->category->name;
                $item->group_name = $item->group->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }

                unset($item->brand);
                unset($item->category);
                unset($item->group);
                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError("An error occurred while getting items. Please try again later.");
        }

    }

    public function homeAllItemWithStar($per_page = 10, $page_number = 1, $filter_type = "created_at", $sorting_type = "desc")
    {
        try {
            $items = Item::with(['category', 'brand'])
                ->where('star', 1)
                ->where('amount', '>', 0)
                ->where('status', 1)
                ->orderBy($filter_type, $sorting_type)
                ->paginate($per_page, ["*"], "page", $page_number);

            $items->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand->name;
                $item->category_name = $item->category->name;
                $item->group_name = $item->group->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->brand);
                unset($item->category);
                unset($item->group);
                return $item;
            });

            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError("An error occurred while getting items. Please try again later.");
        }
    }

    public function homeLatestItems($items_number = 10, $per_page = 10, $page_number = 1)
    {
        try {
            $items = Item::where('amount', '>', 0)
                ->where('status', 1)
                ->latest("created_at")
                ->take($items_number)
                ->paginate($per_page, ["*"], "page", $page_number);

            $items->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand->name;
                $item->category_name = $item->category->name;
                $item->group_name = $item->group->name;
                $item->coin_name = $item->coin->name ;
                if ($item->status == 0) {
                    $item->deletable = 0;
                } else {
                    $item->deletable = 1;
                }
                unset($item->brand);
                unset($item->category);
                unset($item->group);
                return $item;
            });

            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError("An error occurred while getting items. Please try again later.");
        }
    }


    public function advancedSearch(Request $request)
    {
        $sub = Auth::guard("sub")->user();

        $filters = $request->only([
            'group_id',
            'category_id',
            'brand_id',
            'name',
            'is_new',
            'price_min',
            'price_max',
            'star',
            'visitors',
            "status",
            "Minimum_sale"
        ]);

        $perPage = $request->query('per_page', 10);
        $pageNumber = $request->query('page_number', 1);
        $group_id = $request->query('group_id');
        $sortable_column = $request->query('sortable_column', "id");
        $sorting_type = $request->query('sorting_type', "desc");


        $group = $sub->groups()->find($group_id);
        if (!$group) {
            return $this->returnError("Group not found or does not belong to the current user.", 404);
        }

        $query = $group->items();


        if (isset($filters['star'])) {
            $query->where('star', $filters['star']);
        }
        if (isset($filters['Minimum_sale'])) {
            $query->where('Minimum_sale', "<", $filters['Minimum_sale']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_new'])) {
            $query->where('is_new', $filters['is_new']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['visitors'])) {
            $query->where('visitors', '>=', $filters['visitors']);
        }


        $items = $query->orderBy($sortable_column, $sorting_type)->paginate($perPage, ["*"], "page", $pageNumber);


        $items->getCollection()->transform(function ($item) {
            $item->brand_name = $item->brand->name;
            $item->category_name = $item->category->name;
            $item->coin_name = $item->coin->name ;
            $item->deletable = $item->status == 0 ? 0 : 1;

            unset($item->category);
            unset($item->brand);
            return $item;
        });

        return $this->returnData("items", $items->getCollection(), 200, $items);
    }

    public function homeAdvancedSearch(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'name',
            'is_new',
            'star',
        ]);

        $perPage = $request->query('per_page', 10);
        $pageNumber = $request->query('page_number', 1);
        $sortable_column = $request->query('sortable_column', "id");
        $sorting_type = $request->query('sorting_type', "desc");


        $query = Item::where('amount', '>', 0)
            ->where('status', '=', 1);


        if (isset($filters['star'])) {
            $query->where('star', $filters['star']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_new'])) {
            $query->where('is_new', $filters['is_new']);
        }


        $items = $query->orderBy($sortable_column, $sorting_type)
            ->paginate($perPage, ["*"], "page", $pageNumber);


        $items->getCollection()->transform(function ($item) {
            $item->brand_name = $item->brand->name;
            $item->category_name = $item->category->name;
            $item->coin_name = $item->coin->name ;

            $item->deletable = $item->status == 0 ? 0 : 1;


            unset($item->category);
            unset($item->brand);

            return $item;
        });


        return $this->returnData("items", $items->getCollection(), 200, $items);
    }


    public function generalSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "search" => "required|string"
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        try {
            $perPage = $request->query('per_page', 10);
            $pageNumber = $request->query('page_number', 1);
            $search = $request->query('search');
            $items = Item::where('name', 'like', '%' . $search . '%')
                ->orWhere('description', "like", '%' . $search . "%")
                ->paginate($perPage, ["*"], "page", $pageNumber);
            $items->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand->name;
                $item->category_name = $item->category->name;
                $item->group = $item->group->name;
                $item->coin_name = $item->coin->name ;
                $item->deletable = 1;
                unset($item->category);
                unset($item->group);
                unset($item->brand);
                return $item;
            });
            return $this->returnData("items", $items->getCollection(), 200, $items);
        } catch (Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function updateItems($id)
    {
        $item = Item::find($id);
        $item->status = 1;
        $item->save();

    }

    public function makeStar(Request $request)
    {
        $item = Item::find($request->query("id"));
        $item->star = $request->query("status");
        $item->save();

    }

    public function ch($id, $status)
    {
        $item = Item::find($id);
        $item->status = $status;
        $item->save();

    }
}
