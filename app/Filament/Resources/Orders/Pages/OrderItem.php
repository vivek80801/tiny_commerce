<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderItem extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            TextEntry::make('order_id')
                ->state(fn ($record) => 'ORD-#'.$record->order_id),
            TextEntry::make('user.name'),
            TextEntry::make('total')
                ->money('INR', 100),

            RepeatableEntry::make('orderItem')
                ->label('Order Details')
                ->schema([
                    TextEntry::make('product.name'),
                    TextEntry::make('quantity'),
                    TextEntry::make('price')
                        ->money('INR', 100),
                ])
                ->columnSpan('full')
                ->columns(2),
            Section::make('address')
                ->label('Address')
                ->schema([
                    TextEntry::make('address.phone'),
                    TextEntry::make('address.city'),
                    TextEntry::make('address.pin_code'),
                    TextEntry::make('address.house_number'),
                    TextEntry::make('address.address'),
                    TextEntry::make('address.country.name')
                        ->label('Country'),
                    TextEntry::make('address.state.name')
                        ->label('State'),
                    TextEntry::make('address.district.name')
                        ->label('District'),
                ])
                ->columnSpan('full')
                ->columns(2),
        ]);
    }
}
