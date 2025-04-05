<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CountryCodeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('sign-up', [SubscriberController::class, 'signUp']);

Route::post('login', [SubscriberController::class, 'login']);

Route::get("check", [SubscriberController::class, "check"]);

Route::group(["middleware" => "Check:sub"], function () {

    Route::group(["middleware" => 'isActive'], function () {

        Route::get('logout', [SubscriberController::class, 'logout']);

        Route::put('update/{id}', [SubscriberController::class, 'update']);

        Route::put('change-password/{id}', [SubscriberController::class, 'changePassword']);

        Route::post("add-group", [GroupController::class, 'addGroup']);

        Route::get('group-search', [GroupController::class, "searchByName"]);

        Route::get('get-count-brand-item/{brand_id}', [BrandController::class, 'getCountBrandItems']);

        Route::get('get-count-category-item/{category_id}', [CategoryController::class, 'getCountCategroyItems']);

        Route::get('get-count-group-item/{group_id}', [GroupController::class, 'getCountGroupItems']);

        Route::post("add-item", [ItemController::class, 'addItem']);

        Route::get('get-items-by-visitors/{group_id}/{per_page}/{page_number}/{sort_type?}', [ItemController::class, 'getItemsByVisitors']);

        Route::get("get-item-by-is-new/{group_id}/{per_page}/{page_number}/{is_new?}", [ItemController::class, "getItemsByIsNew"]);

        Route::get(
            "get-star-items/{group_id}/{per_page?}/{page_number?}",
            [ItemController::class, "getStarItems"]
        );

        Route::get("get-item-by-id/{item_id}/{group_id}", [ItemController::class, "getItemById"]);

        Route::get("get-item-by-status/{group_id}/{status}/{per_page}/{page_number}", [ItemController::class, "getByStatus"]);

        Route::get("get-item-by-group-id/{group_id}/{per_page}/{page_number}", [ItemController::class, "getByIdGroup"]);

        Route::get("get-item-by-name/{group_id}/{name}/{per_page}/{page_number}", [ItemController::class, "searchByName"]);

        Route::get("search-name-group-id/{group_id}/{name}/{per_page}/{page_number}", [ItemController::class, "searchByNameByIdGroup"]);

        Route::get("get-all-items/{per_page}/{page_number}", [GroupController::class, "getAllGroups"]);

        Route::get("get-groups-items/{per_page}/{page_number}", [GroupController::class, "getAllGroupsWithItems"]);

        Route::get("get-group-with-items/{group_id}/{per_page}/{page_number}", [GroupController::class, "getGroupWithItem"]);

        Route::get("get-all-groups/{per_page}/{page_number}", [GroupController::class, "getAllGroups"]);

        Route::get("get-group-by-id/{group_id}/{per_page?}/{page_number?}", [GroupController::class, "getGroupById"]);

        Route::delete('delete-group/{group_id}', [GroupController::class, "delete"]);

        Route::get('show-deleted-groups', [GroupController::class, "showDeletedGroupes"]);

        Route::get('restore-deleted-group/{group_id}', [GroupController::class, "restoreDeletedGroup"]);

        Route::get('get-all-brands', [BrandController::class, 'getAllBrands']);

        Route::get('get-brand-with-items/{brand_id}/{per_page}/{page_number}', [BrandController::class, 'getBrandWithItems']);

        Route::get("get-brand-by-id/{brand_id}/{per_page?}/{page_number?}", [BrandController::class, "getBrandById"]);

        Route::get('get-category-with-items/{category_id}/{per_page}/{page_number}', [CategoryController::class, 'getCategoryWithItems']);

        Route::get("get-category-by-id/{category_id}/{per_page?}/{page_number?}", [CategoryController::class, "getCategoryById"]);

        Route::get('get-all-categories', [CategoryController::class, 'getAllCategories']);

        Route::delete("delete-item/{item_id}", [ItemController::class, 'deleteItem']);

        Route::put("update-item/{item_id}", [ItemController::class, "updateItem"]);

        Route::post("update-item-image/{item_id}", [ItemController::class, "updateItemImage"]);

        Route::get('get-User-By-Id/{id}', [SubscriberController::class, 'getUserById']);

        Route::get('advanced-search', [ItemController::class, 'advancedSearch']);

        Route::post("add-item-to-cart", [CartController::class, 'addItemToCart']);

        Route::delete('remove-item-from-cart', [CartController::class, "removeItemFromCart"]);

        Route::put("update-item-amout-in-cart", [CartController::class, 'updateItemQuantityInCart']);

        Route::get("checkout", [CartController::class, "checkOut"]);

        Route::get("get-orders", [OrderController::class, "getUserOrders"]);

        Route::get("get-order-items/{order_id}", [OrderController::class, "getOrderItems"]);

        Route::get("get-cart-details", [CartController::class, "getCartWithItems"]);

        Route::delete("remove-item-from-order", [OrderController::class, "removeItemFromOrder"]);

        Route::put("update-order", [OrderController::class, "updateOrderStatus"]);

        Route::put("update-item-quantity-in-order", [OrderController::class, "updateItemQuantityInOrder"]);

        Route::delete("delete-order/{order_id}", [OrderController::class, "deleteOrder"]);

        Route::get('get-coins', [CoinController::class, "getAll"]);
    });

    Route::post('verify', [SubscriberController::class, 'verify']);
});

Route::get('get-country-code', [CountryCodeController::class, "getAll"]);


Route::get("general-search", [ItemController::class, "generalSearch"]);

Route::get("get-item-by-id-home/{item_id}", [ItemController::class, "getItemByIdHome"]);

Route::get('get-items-by-visitors-home', [ItemController::class, 'getItemsByVisitorsHome']);

Route::get("all-brands-with-count/{sorting_type?}/{per_page?}/{page_number?}", [BrandController::class, 'allWithCount']);

Route::get("all-category-with-count/{sorting_type?}/{per_page?}/{page_number?}", [CategoryController::class, 'allWithCount']);

Route::get("all-items-with-star/{per_page?}/{page_number?}/{filter_type?}/{sorting_type}", [ItemController::class, "homeAllItemWithStar"]);

Route::get("latest-items/{items_number?}/{per_page?}/{page_number?}", [ItemController::class, 'homeLatestItems']);

Route::get("get-items-by-range/{min}/{max}/{per_page?}/{page_page?}/{sorting_type?}", [ItemController::class, 'getItemsByRane']);

Route::get('advanced-search-home', [ItemController::class, "homeAdvancedSearch"]);

Route::get("advanced-search-brand", [BrandController::class, "advancedSearch"]);

Route::get("advanced-search-category", [CategoryController::class, "advancedSearch"]);

Route::post("contact-us", [SubscriberController::class, 'contactUs']);

Route::post('reset-password-step-1', [SubscriberController::class, 'rpStep1']);

Route::post('reset-password-step-2', [SubscriberController::class, 'rpStep2']);

Route::put('reset-password-step-3/{id}', [SubscriberController::class, 'rpStep3']);

Route::post('resend-verification', [SubscriberController::class, 'resend']);

Route::post('send-verification', [SubscriberController::class, 'send_email']);

Route::post("add-brand", [BrandController::class, 'addBrand']);

Route::post("add-category", [CategoryController::class, 'addCategory']);

Route::get('get-By-Email/{id}', [SubscriberController::class, 'getUserByEmail']);

Route::get('get-all-brands-home', [BrandController::class, 'getAllBrands']);

Route::get('get-all-categories-home', [CategoryController::class, 'getAllCategories']);

Route::get("update/{id}", [ItemController::class, 'updateItems']);

Route::get("star", [ItemController::class, "makeStar"]);

Route::post("add-page", [PageController::class, 'addPage']);

Route::get("get-page-by-id/{id}", [PageController::class, "getPageById"]);

Route::get("get-all-pages", [PageController::class, "getAllPages"]);

Route::post("add-service", [ServiceController::class, 'addService']);

Route::get("get-service-by-id/{id}", [ServiceController::class, "getServiceById"]);

Route::get("get-all-services", [ServiceController::class, "getAllServices"]);

Route::post("add-slider", [SliderController::class, 'addSlider']);

Route::get("get-slider-by-id/{id}", [SliderController::class, "getsliderById"]);

Route::get("get-all-sliders", [SliderController::class, "getAllsliders"]);

Route::post("add-contact", [ContactController::class, 'addContact']);

Route::get("get-contact-by-id/{id}", [ContactController::class, "getContactById"]);

Route::get("get-all-contact", [ContactController::class, "getAllContacts"]);

Route::get("ch/{id}/{status}", [ItemController::class, "ch"]);
