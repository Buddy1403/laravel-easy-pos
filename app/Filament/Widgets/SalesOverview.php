<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currency_symbol = config('settings.currency_symbol');

        // Fetch orders within the last 30 days with their items
        $orders = Order::with('items')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();

        $totalOrders = $orders->count();
        $totalCustomers = Customer::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        $totalSellingPrice = 0;
        $totalRegularPrice = 0;

        // Compute sales and cost
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // make sure these columns exist in your order_items table
                $selling = $item->selling_price ?? $item->price ?? 0;
                $regular = $item->product->regular_price ?? 0;

                $totalSellingPrice += $selling * ($item->quantity ?? 1);
                $totalRegularPrice += $regular * ($item->quantity ?? 1);
            }
        }

        $totalIncome = $totalSellingPrice - $totalRegularPrice;

        return [
            Stat::make('Orders (30 days)', $totalOrders)
                ->description('Total orders in the last 30 days')
                ->descriptionIcon('heroicon-o-inbox-stack', IconPosition::Before)
                ->chart([1, 5, 10, 50])
                ->color('success'),

            Stat::make('Sales (30 days)', $currency_symbol.number_format($totalSellingPrice, 2))
                ->description('Total sales in the last 30 days (selling price)')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart([5, 15, 30, 60])
                ->color('info'),

            Stat::make('Income (30 days)', $currency_symbol.number_format($totalIncome, 2))
                ->description('Profit = Sales - Cost in the last 30 days')
                ->descriptionIcon('heroicon-o-chart-bar', IconPosition::Before)
                ->chart([3, 12, 25, 50])
                ->color('success'),
        ];
    }
}
