<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    use ResponseTrait;
    public function getAllBrands()
    {
        return $this->returnData("", Brand::all());
    }

    public function getBrandWithItems($id, $per_page, $page_number)
    {

        try {
            $brand = Brand::findOrFail($id);
            $items = $brand->items()->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->category_name = $item->category->name;
                $item->group_name = $item->group->name;
                unset($item->group);
                unset($item->category);
                return $item;
            });
            $brand = [
                "brand" => $brand,
                "items" => $items->getCollection()
            ];
            return $this->returnData("these all items in this brand ", $brand, 200, $items);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function getCountBrandItems($id)
    {

        try {
            $count = Brand::findOrFail($id)->items()->count("id");
            return $this->returnData("the number of items : ", $count);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function allWithCount($sort_type = "desc", $per_page = 10, $page_number = 1)
    {
        try {
            $all = Brand::select(["*"])
                ->withCount(['items' => function ($query) {
                    $query->where('amount', '>', 0)
                          ->where('status', 1);
                }])
                ->having('items_count', '>', 0)
                ->orderBy('items_count', $sort_type)
                ->paginate($per_page, ["*"], "page", $page_number);

            return $this->returnData("", $all->getCollection(), 200, $all);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }




    public function getBrandById($id, $per_page = 10, $page_number = 1)
    {
        try {
            $brand = Brand::findOrFail($id);

            $itemsQuery = $brand->items();
            $items = $itemsQuery->paginate($per_page, ['*'], 'page', $page_number);

            $meta = $items;


            $brandData = [
                'brand' => $brand,
                'items' => $items->items(),
            ];

            return $this->returnData("brand with items", $brandData, 200, $meta);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }



    public function addBrand(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "name" => "string|required",
                'image' => 'file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                "description" => "nullable|string",
                "color" => "string"
            ]
        );
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $brand = Brand::create([
                "name" => $request->name,
                'image' =>  photo($request, "brand", "brandPhoto"),
                "description" => $request->description,
                "color" => $request->color
            ]);

            return $this->returnData("your brand created successfully :)", $brand);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function advancedSearch(Request $request) {
        $perPage = $request->query('per_page', 10);
        $pageNumber = $request->query('page_number', 1);

        $query = Brand::query();


        if ($request->query('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        $brands = $query->select(["*"])
            ->withCount(['items' => function ($query) {
                $query->where('amount', '>', 0)
                      ->where('status', 1);
            }])
            ->paginate($perPage, ["*"], "page", $pageNumber);

        return $this->returnData("brand and count", $brands->getCollection(), 200, $brands);
    }


}
