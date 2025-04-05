<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Auth;
class GroupController extends Controller
{
    use ResponseTrait;
    public function addGroup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => " required|string|max:30|min:3",
            "address" => "string| max:255|min:3",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $sub = Auth::guard('sub')->user();
            $group = $sub->groups()->create([
                "name" => $request->name,
                "description" => $request->description,
                "address" => $request->address,
            ]);

            return $this->returnData("your group is created succesfully :)", $group);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function getAllGroupsWithItems($per_page, $page_number)
    {
        $sub = auth("sub")->user();


        $groups = $sub->groups()->paginate(
            $per_page,
            ["*"],
            "page",
            $page_number
        );


        $groups->getCollection()->transform(function ($group) use ($per_page, $page_number) {

            $items = $group->items()
                ->paginate($per_page, ["*"], "page", $page_number)
                ->getCollection()
                ->transform(function ($item) {

                    $item->group_name = $item->group->name ?? null;
                    $item->category_name = $item->category->name ?? null;
                    $item->brand_name = $item->brand->name ?? null;
                    $item->coin_name = $item->coin ;

                    unset($item->group);
                    unset($item->category);
                    unset($item->brand);

                    return $item;
                });

            $group->items = $items;

            return $group;
        });


        return $this->returnData("", $groups->items(), 200, $groups);
    }



    public function delete($id)
    {
        try {
            $sub = Auth::guard("sub")->user();

            $group = Group::findOrFail($id);
            if ($sub->id == $group->sub_id) {
                $items = $group->items;
                foreach ($items as $item) {
                    if ($item->status != 0) {
                        return $this->returnError("you can not delete this group any more :(");
                    }
                }
                $group->delete();
                return $this->returnSuccess('this group deleted successfully :)');
            } else {
                return $this->returnError("you dont have permission to delete this group :(");
            }
        } catch (\Exception $e) {
            return $this->returnError("An error occurred while deleting the group. Please try again later.");
        }
    }

    public function updateGroup(Request $request, $id)
    {
        try {
            $sub = Auth::guard("sub")->user();

            $group = Group::findOrFail($id);
            if ($sub->id == $group->sub_id) {
                $items = $group->items;
                foreach ($items as $item) {
                    if ($item->status != 0) {
                        return $this->returnError("you can not delete this group any more :(");
                    }
                }
                $group->update();
                return $this->returnSuccess('this group deleted successfully :)');
            } else {
                return $this->returnError("you dont have permission to delete this group :(");
            }
        } catch (\Exception $e) {
            return $this->returnError("An error occurred while deleting the group. Please try again later.");
        }
    }
    public function showDeletedGroupes()
    {
        try {

            $sub = Auth::guard("sub")->user();
            $deletesGroups = $sub->groups()->onlyTrashed()->get();
            return $this->returnData('', $deletesGroups);
        } catch (\Exception $e) {
            return $this->returnError("An error occurred while getting deleted  groupes. Please try again later.");

        }
    }


    public function restoreDeletedGroup($id)
    {
        try {
            $group = Group::withTrashed()->findOrFail($id);
            $group->restore();
            return $this->returnSuccess('this item restored successfully :)');

        } catch (\Exception $e) {
            return $this->returnError("An error occurred while getting deleted  groupes. Please try again later.");

        }
    }


    public function searchByName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => " string|max:30",
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }
        $per_page = $request->query('per_page', 10);
        $page_number = $request->query('page_number', 1);
        $sortable_column = $request->query('sortable_column', "id");
        $sorting_type = $request->query('sorting_type', "desc");
        $name = $request->query('name');
        $sub_group = Auth::guard('sub')->user()
            ->groups()
            ->where('name', 'like', '%' . $name . '%')
            ->orderBy($sortable_column, $sorting_type)->withCount("items")
            ->paginate($per_page, ["*"], "page", $page_number);


        return $this->returnData("", $sub_group->getCollection(), 200, $sub_group);
    }



    public function getGroupWithItem($group_id, $per_page, $page_number)
    {

        try {

            $sub = Auth::guard("sub")->user();
            $group = $sub->groups()->findOrFail($group_id);
            $items = $group->items()->paginate($per_page, ["*"], "page", $page_number);
            $items->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand()->first()->name;
                $item->category_name = $item->category()->first()->name;
                $item->group_name = $item->group->name ?? null;
                $item->coin_name = $item->coin ;
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });
            $group = [
                "group" => $group,
                "items" => $items->getCollection()
            ];
            return $this->returnData("these are items for this group : ", $group, 200, $items);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }


    public function getGroupById($id, $per_page = 10, $page_number = 1)
    {
        try {

            $group = Group::findOrFail($id);


            $itemsQuery = $group->items();
            $items = $itemsQuery->paginate($per_page, ['*'], 'page', $page_number);


            $items->getCollection()->transform(function ($item) {
                $item->group_name = $item->group->name ?? null;
                $item->category_name = $item->category->name ?? null;
                $item->brand_name = $item->brand->name ?? null;

                $item->coin_name = $item->coin;
                unset($item->group);
                unset($item->category);
                unset($item->brand);

                return $item;
            });


            $meta = $items;


            $groupData = [
                'group' => $group,
                'items' => $items->items(),
            ];

            return $this->returnData("group with items", $groupData, 200, $meta);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }




    public function getAllGroups($per_page, $page_number)
    {

        try {
            $sub = Auth::guard("sub")->user();

            $groups = $sub->groups()->paginate($per_page, ["*"], "page", $page_number);

            return $this->returnData("your all groups", $groups->getCollection(), 200, $groups);

        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }



    public function getCountGroupItems($id)
    {

        try {
            $count = Group::findOrFail($id)->items()->count("id");
            return $this->returnData("the number of items : ", $count);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }
}
