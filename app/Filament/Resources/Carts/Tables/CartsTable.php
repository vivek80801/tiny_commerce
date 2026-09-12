<?php

namespace App\Filament\Resources\Carts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product'),
                TextColumn::make('user.name')
                    ->label('User'),

                TextColumn::make('product.price')
                    ->label('Price')
                    ->money('INR', 100),
                TextColumn::make('quantity'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->state(function ($record) {
                        return $record->product->price * $record->quantity;
                    })
                    ->money('INR', 100),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
