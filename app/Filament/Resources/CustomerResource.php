<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationLabel = 'Store';

    protected static ?string $pluralLabel = 'Stores';

    protected static ?string $modelLabel = 'Store';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Site Name')
                    ->required()
                    ->maxLength(20),
                TextInput::make('last_name')
                    ->label('Store Name')
                    ->maxLength(20),
                TextInput::make('email')
                    ->email()
                    ->nullable(),
                TextInput::make('phone')
                    ->tel()
                    ->nullable(),
                Textarea::make('address')
                    ->nullable(),
                FileUpload::make('avatar')
                    ->image()
                    ->visibility('public')
                    ->disk('public_uploads')
                    ->directory('avatars')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Site Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Store Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('orders_count')
                    ->label('Order Count')
                    ->counts('orders')
                    ->sortable(),
                TextColumn::make('created_at')->sortable()->dateTime(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                // DeleteAction::make(),
            ]);
        // ->toolbarActions([
        //     DeleteBulkAction::make(),
        // ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
