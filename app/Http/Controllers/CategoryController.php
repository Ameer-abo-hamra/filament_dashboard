<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\Collection;
use Validator;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function getAllCategories()
    {
        return $this->returnData("", Category::all());
    }
    public function getCategoryWithItems($id, $per_page, $page_number)
    {

        try {
            $data = Category::findOrFail($id)->items()->paginate($per_page, ["*"], $page_number);
            return $this->returnData("these all items in this category ", $data->getCollection(), 200, $data);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    public function getCountCategroyItems($id)
    {

        try {
            $count = Category::findOrFail($id)->items()->count("id");
            return $this->returnData("the number of items : ", $count);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function allWithCount($sorting_type = "desc", $per_page = 10, $page_number = 1)
    {
        try {
            $all = Category::select(["*"])
                ->withCount([
                    'items' => function ($query) {
                        $query->where('amount', '>', 0)
                            ->where('status', 1);
                    }
                ])
                ->having('items_count', '>', 0)
                ->orderBy('items_count', $sorting_type)
                ->paginate($per_page, ["*"], "page", $page_number);

            return $this->returnData("", $all->getCollection(), 200, $all);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    public function getCategoryById($id, $per_page = 10, $page_number = 1)
    {
        try {
            $category = Category::findOrFail($id);

            $itemsQuery = $category->items();
            $items = $itemsQuery->paginate($per_page, ['*'], 'page', $page_number);

            $meta = $items;


            $categoryData = [
                'brand' => $category,
                'items' => $items->items(),
            ];

            return $this->returnData("category with items", $categoryData, 200, $meta);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    public function addCategory(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "name" => "string|required",
                'image' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                "description" => "nullable|string",
                "color" => "string"
            ]
        );
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $brand = Category::create([
                "name" => $request->name,
                'image' => photo($request, "category", "categories"),
                "description" => $request->description,
                "color" => $request->color
            ]);

            return $this->returnData("your category created successfully :)", $brand);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function advancedSearch(Request $request) {
        $perPage = $request->query('per_page', 10);
        $pageNumber = $request->query('page_number', 1);

        $query = Category::query();


        if ($request->query('name')) {
            $query->where('name', 'like', '%' . $request->query('name') . '%');
        }

        $categories = $query->select(["*"])
            ->withCount(['items' => function ($query) {
                $query->where('amount', '>', 0)
                      ->where('status', 1);
            }])
            ->paginate($perPage, ["*"], "page", $pageNumber);

        return $this->returnData("categories and count", $categories->getCollection(), 200, $categories);
    }

}
