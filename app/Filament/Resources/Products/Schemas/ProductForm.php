<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("name")->required(),
                TextInput::make("price")->required()->numeric(),
                TextInput::make("quantity")->required()->numeric(),
                Textarea::make('description')
                    ->required()
                    ->autosize()
                    ->maxLength(500),

                Select::make("category_id")
                    ->relationship("category", "name")
                    ->required()
                    ->searchable()
                    ->preload()
                ,

                Fieldset::make("product.image")
                    ->label("Image")
                    ->relationship("image")
                    ->markAsRequired()
                    ->schema([
                        FileUpload::make("filename")
                            ->disk("upload")
                    ])
                ,
            ]);
    }
}
