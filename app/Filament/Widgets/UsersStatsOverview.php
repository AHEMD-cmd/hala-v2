<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Admins', User::role('super_admin')->count())
                ->description('System administrators')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),

            Stat::make('Sales Staff', User::role('sales')->count())
                ->description('Sales employees')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Inventory Staff', User::role('inventory')->count())
                ->description('Inventory employees')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}