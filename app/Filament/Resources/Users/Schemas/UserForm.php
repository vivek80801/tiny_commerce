<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')
                    ->email()
                    ->unique('users', 'email')
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->minLength(5)
                    ->maxLength(20)
                    ->revealable()
                    ->confirmed()
                    ->required(),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->dehydrated(false),

                Checkbox::make('is_admin')->label('Admin'),
            ]);
    }
}
