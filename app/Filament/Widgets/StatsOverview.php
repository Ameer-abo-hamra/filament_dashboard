<?php

namespace App\Filament\Widgets;

use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users Last Month', Subscriber::whereDate('created_at', '>=', now()->subMonth())->count())
            ->description('Active users in last month')
            ->chart([
                'Week 1' => Subscriber::whereDate('created_at', '>=', now()->subWeek())->count(),
                'Week 2' => Subscriber::whereDate('created_at', '>=', now()->subWeeks(2))
                    ->whereDate('created_at', '<', now()->subWeek())->count(),
                'Week 3' => Subscriber::whereDate('created_at', '>=', now()->subWeeks(3))
                    ->whereDate('created_at', '<', now()->subWeeks(2))->count(),
                'Week 4' => Subscriber::whereDate('created_at', '>=', now()->subWeeks(4))
                    ->whereDate('created_at', '<', now()->subWeeks(3))->count()
            ])
            ->color('info')
            ->url(route('filament.admin.resources.subscribers.index', [
                'tableFilters[created_at][value]' => now()->subMonth()->toDateString()
            ])),

            Stat::make('Total Orders price', \App\Models\Order::sum('total'))
                ->description('Total value of all orders')
                ->chart([
                    'Week 1' => \App\Models\Order::whereDate('created_at', '>=', now()->subWeek())->sum('total'),
                    'Week 2' => \App\Models\Order::whereDate('created_at', '>=', now()->subWeeks(2))
                        ->whereDate('created_at', '<', now()->subWeek())->sum('total'),
                    'Week 3' => \App\Models\Order::whereDate('created_at', '>=', now()->subWeeks(3))
                        ->whereDate('created_at', '<', now()->subWeeks(2))->sum('total'),
                    'Week 4' => \App\Models\Order::whereDate('created_at', '>=', now()->subWeeks(4))
                        ->whereDate('created_at', '<', now()->subWeeks(3))->sum('total')
                ])
                ->color('info')
                ->url(route('filament.admin.resources.orders.index')),

            Stat::make('Total Users', Subscriber::count())
                ->color('success')
                ->description('Total number of registered users')
                ->color('info')
                ->url(route('filament.admin.resources.subscribers.index')),

            Stat::make('Inactive Users', Subscriber::where('is_active', false)->count())
                ->description('Users pending admin activation')
                ->color('danger')
                ->url(route('filament.admin.resources.subscribers.index', [
                    'tableFilters[is_active][value]' => false
                ])),

            Stat::make('Unverified Users', Subscriber::where('is_verified', false)->count())
                ->description('Users pending email verification')
                ->color('warning')
                ->url(route('filament.admin.resources.subscribers.index', [
                    'tableFilters[is_verified][value]' => false
                ])),

            // Items stats by status
            Stat::make('Pending Items', \App\Models\Item::where('status', 0)->count())
                ->description('Items awaiting review')
                ->color('warning')
                ->url(route('filament.admin.resources.items.index', [
                    'tableFilters[status][value]' => 0
                ])),

            Stat::make('Accepted Items', \App\Models\Item::where('status', 1)->count())
                ->description('Approved items')
                ->color('success')
                ->url(route('filament.admin.resources.items.index', [
                    'tableFilters[status][value]' => 1
                ])),

            Stat::make('Rejected Items', \App\Models\Item::where('status', 2)->count())
                ->description('Rejected items')
                ->color('danger')
                ->url(route('filament.admin.resources.items.index', [
                    'tableFilters[status]' => 2
                ])),

            // Orders stats by status
            Stat::make('Pending Orders', \App\Models\Order::where('status', 0)->count())
                ->description('Orders awaiting processing')
                ->color('warning')
                ->url(route('filament.admin.resources.orders.index', [
                    'tableFilters[status][value]' => 0
                ])),

            Stat::make('Accepted Orders', \App\Models\Order::where('status', 1)->count())
                ->description('Processed orders')
                ->color('success')
                ->url(route('filament.admin.resources.orders.index', [
                    'tableFilters[status][value]' => 1
                ])),

            Stat::make('Rejected Orders', \App\Models\Order::where('status', 2)->count())
                ->description('Rejected orders')
                ->color('danger')
                ->url(route('filament.admin.resources.orders.index', [
                    'tableFilters[status][value]' => 2
                ])),
        ];
    }
}
