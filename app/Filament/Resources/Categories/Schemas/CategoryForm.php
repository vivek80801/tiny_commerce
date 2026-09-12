<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Fieldset::make('category.image')
                    ->label('Image')
                    ->relationship('image')
                    ->markAsRequired()
                    ->schema([
                        FileUpload::make('filename')
                            ->disk('upload'),
                    ]),
            ]);
    }
}
