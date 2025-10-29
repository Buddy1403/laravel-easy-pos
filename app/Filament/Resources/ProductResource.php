<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->required()
                    ->unique(Product::class, 'barcode', ignoreRecord: true),
                TextInput::make('regular_price')
                    ->numeric()
                    ->required(),
                TextInput::make('selling_price')
                    ->numeric()
                    ->required(),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0)
                    ->default(1)
                    ->required(),
                TextInput::make('tax')
                    ->label('Tax (%)')
                    ->suffixIcon('heroicon-o-information-circle')
                    ->helperText('Example: 5 for 5% VAT/GST.')
                    ->numeric()
                    ->default(0.00),
                FileUpload::make('image')
                    ->disk('public_uploads')
                    ->panelLayout('grid')
                    ->visibility('public'),
                Toggle::make('status')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->width(250)
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')->disk('public_uploads')
                    ->size(50)
                    ->square(),
                TextColumn::make('barcode')->searchable(),
                TextInputColumn::make('quantity')->type('number')
                    ->sortable()
                    ->width(10)
                    ->rules(['required', 'integer', 'min:1']),
                TextColumn::make('regular_price')->sortable(),
                TextColumn::make('selling_price')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exports([
                    ExcelExport::make()
                        ->withFilename(fn ($resource) => $resource::getModelLabel().'-'.date('Y-m-d'))
                        ->withWriterType(Excel::CSV)
                        ->withColumns([
                            Column::make('name')->heading('Name'),
                            Column::make('barcode')->heading('Barcode'),
                            Column::make('regular_price')->heading('Regular Price'),
                            Column::make('selling_price')->heading('Selling Price'),
                            Column::make('tax')->heading('Tax'),
                            Column::make('quantity')->heading('Quantity'),
                        ]),
                ]),
            ]);
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
            'index' => ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
