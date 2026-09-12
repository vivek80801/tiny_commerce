<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make("user_id")
                    ->relationship("user", "name")
                    ->searchable()
                    ->preload()
                    ->required()
                ,
                Section::make("Address")
                    ->relationship("address")
                    ->schema([
                        TextInput::make("phone")
                            ->label("Phone Number")
                            ->required()
                        ,
                        TextInput::make("city")
                            ->label("City")
                            ->required()
                        ,
                        TextInput::make("pin_code")
                            ->label("Pin Code")
                            ->required()
                        ,
                        TextInput::make("house_number")
                            ->label("House Number")
                            ->required()
                        ,
                        Textarea::make("address")
                            ->label("Address")
                            ->required()
                        ,
                        Select::make("country_id")
                            ->relationship("country", "name")
                            ->required()
                            ->searchable()
                            ->label("Country")
                        ,
                        Select::make("state_id")
                            ->relationship("state", "name")
                            ->required()
                            ->searchable()
                            ->label("State")
                        ,

                        Select::make("district_id")
                            ->relationship("district", "name")
                            ->required()
                            ->searchable()
                            ->label("District")
                        ,
                    ])
                    ->columnSpan("full")
                    ->columns(2)
                    ->collapsible()
                ,
            ]);
    }
}
