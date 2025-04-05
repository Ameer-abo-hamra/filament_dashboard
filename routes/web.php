<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

// Route::get('/a', function () {
//     return Auth::guard('admin')->user();
// });

Route::get("/" , function() {
    return redirect("admin/login");
});

Route::get("a" , function() {
    return Auth::guard("admin")->user()->id;
});


Route::get('/run-artisan-commands', function () {
    // الأوامر التي تريد تشغيلها
    $commands = [
        'config:clear',
        'cache:clear',
        'config:cache',
    ];

    $output = '';

    // تشغيل كل الأوامر
    foreach ($commands as $command) {
        $output .= "Running command: php artisan {$command} <br>";
        $artisanOutput = Artisan::call($command);
        $output .= nl2br(Artisan::output()) . '<br>';
    }

    return $output;
});

Route::get('/proxy-resource', function (Request $request) {
    $url = $request->query('url'); // استلام الرابط من الطلب

    // التحقق من صحة الرابط
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['error' => 'Invalid URL'], 400);
    }

    try {
        // جلب المحتوى من الرابط الخارجي
        $response = Http::get($url);

        if ($response->successful()) {
            // إعادة إرسال المحتوى مع نوعه
            return response($response->body(), 200)
                ->header('Content-Type', $response->header('Content-Type'));
        } else {
            return response()->json(['error' => 'Failed to fetch resource'], $response->status());
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching the resource'], 500);
    }
});
