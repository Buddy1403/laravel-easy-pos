<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

    // protected function getHeaderActions(): array
    // {

    //     return [
    //         ExportAction::make()
    //             ->exports([
    //                 ExcelExport::make()
    //                     ->fromTable()
    //                     ->withFilename(fn ($resource) => $resource::getModelLabel().'-'.date('Y-m-d'))
    //                     ->withWriterType(Excel::CSV)
    //                     ->withColumns([
    //                         Column::make('customer.phone')->heading('Mobile'),
    //                         Column::make('customer.email')->heading('Email'),
    //                         Column::make('customer.address')->heading('Address'),
    //                         Column::make('updated_at'),
    //                     ]),
    //             ]),
    //     ];
    // }

    protected function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }
}
