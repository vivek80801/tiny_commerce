<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(),
                TextInput::make('confirm password')
                    ->password()
                    ->revealable()
                    ->hiddenOn(Operation::Edit)
                    ->visibleOn(Operation::Create)
                    ->required(),
                Checkbox::make('admin'),
            ]);
    }
}
