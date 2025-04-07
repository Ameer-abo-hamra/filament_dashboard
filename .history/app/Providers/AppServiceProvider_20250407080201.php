<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\ServiceProvider;
use App\Models\{Item, Category, Group, Brand, Subscriber};
use App\Observers\GeneralObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Item::observe(GeneralObserver::class);
        Group::observe(GeneralObserver::class);
        Subscriber::observe(GeneralObserver::class);
        Brand::observe(GeneralObserver::class);
        Category::observe(GeneralObserver::class);
        Order::observe(GeneralObserver::class);
        Model::unguard();
        if (app()->environment('production')) {
            URL::forceScheme('https'); // يجبر جميع الروابط على https
        }
    }
}
