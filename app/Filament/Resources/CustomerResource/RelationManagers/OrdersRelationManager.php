<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('total_price'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
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
                ]),
            ])
            ->defaultSort('id', 'DESC');
    }
}
