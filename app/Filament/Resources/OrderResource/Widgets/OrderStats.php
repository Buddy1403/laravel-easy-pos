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
        $currency_symbol = config('settings.currency_symbol');
        $orders = $this->getPageTableQuery()->with('items')->get();
        // $sales = $orders?->items->first()?->price ?? 0;
        $totalOrders = $orders->count();

        $totalSellingPrice = 0;
        $totalRegularPrice = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $totalSellingPrice += $item->price * $item->quantity;
                $totalRegularPrice += $item->product->regular_price * $item->quantity;
            }
        }

        $totalIncome = $totalSellingPrice - $totalRegularPrice;

        return [
            Stat::make('Total orders', $this->getPageTableQuery()->count())
                ->description('Total orders')
                ->descriptionIcon('heroicon-o-inbox-stack', IconPosition::Before)
                ->chart([1, 5, 10, 50])
                ->color('success'),
            Stat::make('Sales', $currency_symbol.number_format($totalSellingPrice, 2))
                ->description('Total sales')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart([1, 5, 30, 50])
                ->color('info'),
            Stat::make('Income', $currency_symbol.number_format($totalIncome, 2))
                ->description('Total income')
                ->descriptionIcon('heroicon-o-banknotes', IconPosition::Before)
                ->chart([1, 5, 30, 50])
                ->color('info'),
        ];
    }
}
