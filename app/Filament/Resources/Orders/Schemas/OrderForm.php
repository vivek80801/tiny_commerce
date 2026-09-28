<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Repeater::make("order_item")
                    ->relationship("orderItem")
                    ->schema([
                        Select::make('product_id')
                            ->relationship("product","name")
                            ->label("Product")
                            ->searchable()
                            ->live()
                            ->preload()
                            ->required()
                            ->afterStateUpdated(
                                function($state, Set $set, Get $get)
                                {
                                    if(!$state)
                                    {
                                        $set('price', null);
                                        return;
                                    }

                                    $product = Product::find($state);
                                    $set('price', $product?->getPrice());

                                    $set("amount", $product
                                        ? (float)$product?->getPrice() * (float) ($get("quantity") ? : (float) 0)
                                        : (float) 0
                                    );
                                }
                            )
                        ,

                        TextInput::make("price")
                            ->label("Price")
                            ->disabled()
                            ->dehydrated()
                            ->numeric()
                            ->required()
                            ->live()
                            ->prefix('₹')
                        ,

                        TextInput::make("quantity")
                            ->label("Quantity")
                            ->live()
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->afterStateUpdated(
                                function($state, Set $set, Get $get)
                                {
                                    $product = Product::find($get("product_id"));

                                    $set('price', $product?->getPrice());

                                    $set("amount", $product
                                        ? ((int) $product?->price * (int) ($get("quantity") ? :  0)) / 100
                                        : 0
                                    );
                                }
                            )
                        ,

                        TextInput::make("amount")
                            ->label("Amount")
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->prefix('₹')
                        ,

                    ])->columnSpan('full')
                ,

                Section::make('Address')
                    ->relationship('address')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->required(),

                        TextInput::make('city')
                            ->label('City')
                            ->required(),

                        TextInput::make('pin_code')
                            ->label('Pin Code')
                            ->required(),

                        TextInput::make('house_number')
                            ->label('House Number')
                            ->required(),

                        Textarea::make('address')
                            ->label('Address')
                            ->required(),

                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required()
                            ->searchable()
                            ->label('Country'),

                        Select::make('state_id')
                            ->relationship('state', 'name')
                            ->required()
                            ->searchable()
                            ->label('State'),

                        Select::make('district_id')
                            ->relationship('district', 'name')
                            ->required()
                            ->searchable()
                            ->label('District'),
                    ])
                    ->columnSpan('full')
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
