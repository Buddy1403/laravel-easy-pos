<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListOrders::class;
    }

    protected function getStats(): array
    {
        $currencySymbol = config('settings.currency_symbol');

        // Retrieve all orders with their items
        $orders = $this->getPageTableQuery()->with('items.product')->get();
        $totalOrders = $orders->count();

        $totalSellingPrice = 0;
        $totalRegularPrice = 0;

        // Compute total sales and cost
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $sellingPrice = $item->price ?? 0;
                $regularPrice = $item->product->regular_price ?? 0;
                $quantity = $item->quantity ?? 1;

                $totalSellingPrice += $sellingPrice * $quantity;
                $totalRegularPrice += $regularPrice * $quantity;
            }
        }

        $totalIncome = $totalSellingPrice - $totalRegularPrice;

        return [
            Stat::make('🧾 Total Orders', number_format($totalOrders))
                ->description('Overall number of orders')
                ->descriptionIcon('heroicon-o-inbox-stack', IconPosition::Before)
                ->chart([5, 10, 25, 50, 100])
                ->color('success'),

            Stat::make('💰 Total Sales', $currencySymbol.number_format($totalSellingPrice, 2))
                ->description('Combined selling price of all items')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart([10, 30, 60, 80, 100])
                ->color('info'),

            Stat::make('📈 Total Income', $currencySymbol.number_format($totalIncome, 2))
                ->description('Profit = Sales - Cost')
                ->descriptionIcon('heroicon-o-chart-bar', IconPosition::Before)
                ->chart([5, 15, 45, 70, 95])
                ->color($totalIncome >= 0 ? 'success' : 'danger'),
        ];
    }
}
