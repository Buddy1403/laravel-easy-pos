<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.pages.edit-order';

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('print')
            //     ->label('Print')
            //     ->livewireClickHandlerEnabled(false)
            //     ->extraAttributes(fn (Order $record) => [
            //         'x-data' => '{}',
            //         'x-on:click' => new HtmlString("() => {
            //         console.log('Printing: ".url('print/'.$record->id)."');
            //         printJS({ printable: '".url('print/'.$record->id)."', type: 'pdf' });
            //     }"),
            //         'type' => 'button', // prevents default form submit
            //         'class' => 'md:flex hidden',
            //     ])
            //     ->icon('heroicon-o-printer')
            //     ->color('success'),

            // Action::make('print')
            //     ->label('Preview')
            //     ->url(fn($record)=> "/print/" . $record->id, shouldOpenInNewTab:true)
            //     ->icon('heroicon-o-document-text')
            //     ->color('success'),
            DeleteAction::make()
                ->label('Void Order'),
        ];
    }
}
