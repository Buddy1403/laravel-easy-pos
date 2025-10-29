<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Imports\ProductsImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Throwable;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Import Products')
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    FileUpload::make('file')
                        ->disk('public_uploads')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {

                    try {
                        Excel::import(new ProductsImport, public_path('uploads/'.$data['file']));
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Import Failed!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                        unlink(public_path('uploads/'.$data['file']));

                        return;
                    }
                    Notification::make()
                        ->title('Products Imported Successfully!')
                        ->success()
                        ->send();
                    unlink(public_path('uploads/'.$data['file']));

                }),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->withFilename(fn ($resource) => $resource::getModelLabel().'-'.date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                        ->withColumns([
                            Column::make('name')->heading('Name'),
                            Column::make('barcode')->heading('Barcode'),
                            Column::make('regular_price')->heading('Regular Price'),
                            Column::make('selling_price')->heading('Selling Price'),
                            Column::make('quantity')->heading('Quantity'),
                        ]),
                ]),
            CreateAction::make()->color('success'),
        ];
    }
}
