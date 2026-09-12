<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Name'),
                TextColumn::make('order_id')
                    ->label('Order Id')
                    ->state(fn ($record) => 'ORD-#'.$record->order_id),
                TextColumn::make('total')
                    ->money('INR', 100),
                TextColumn::make('address.phone')
                    ->label('Phone')
                    ->limit(2),
                TextColumn::make('address.address')
                    ->limit(5),
                TextColumn::make('address.city')
                    ->label('City')
                    ->limit(5),
                TextColumn::make('address.state.name')
                    ->label('State')
                    ->limit(5),
                TextColumn::make('address.district.name')
                    ->label('District')
                    ->limit(5),
                TextColumn::make('address.country.name')
                    ->label('Country')
                    ->limit(2),
                TextColumn::make('address.pin_code')
                    ->label('Pin Code'),
            ])->recordUrl(function ($record) {
                return route('filament.admin.resources.orders.view', $record->id);
            })
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
