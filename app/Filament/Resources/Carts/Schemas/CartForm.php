<?php

namespace App\Filament\Resources\Carts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make("product_id")
                    ->relationship("product", "name")
                    ->searchable()
                    ->preload()
                    ->required()
                ,
                Select::make("user_id")
                    ->relationship("user", "name")
                    ->searchable()
                    ->preload()
                    ->required()
                ,
                TextInput::make("quantity")
                    ->numeric()
                    ->required()
                ,
            ]);
    }
}
