<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency_symbol = config('settings.currency_symbol');

        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.first_name')
                    ->label('Site Name')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->customer->first_name),
                TextColumn::make('customer.last_name')
                    ->label('Store Name')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->customer->last_name),
                TextColumn::make('items.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->formatStateUsing(fn ($record) => $currency_symbol.$record->total_price)->sortable(),
                TextColumn::make('created_at')->sortable()->dateTime(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('From Date'),
                        DatePicker::make('end_date')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];

                        if (! empty($data['start_date'])) {
                            $indicators[] = 'From: '.$data['start_date'];
                        }

                        if (! empty($data['end_date'])) {
                            $indicators[] = 'To: '.$data['end_date'];
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label('Admin Password')
                            ->password()
                            ->required()
                            ->rule(function () {
                                return function (string $attribute, $value, $fail) {
                                    if (! \Hash::check($value, auth()->user()->password)) {
                                        $fail('Incorrect admin password.');
                                    }
                                };
                            }),
                    ])
                    ->modalHeading('Confirm Deletion')
                    ->modalDescription('Please enter your admin password to delete this order.'),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->label('Admin Password')
                            ->password()
                            ->required()
                            ->rule(function () {
                                return function (string $attribute, $value, $fail) {
                                    if (! \Hash::check($value, auth()->user()->password)) {
                                        $fail('Incorrect admin password.');
                                    }
                                };
                            }),
                    ])
                    ->modalHeading('Confirm Deletion')
                    ->modalDescription('Please enter your admin password to delete this order.'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn ($resource) => $resource::getModelLabel().'-'.date('Y-m-d'))
                            ->withWriterType(Excel::CSV)
                            ->withColumns([
                                Column::make('customer.phone')->heading('Mobile'),
                                Column::make('customer.email')->heading('Email'),
                                Column::make('customer.address')->heading('Address'),
                                Column::make('updated_at'),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
