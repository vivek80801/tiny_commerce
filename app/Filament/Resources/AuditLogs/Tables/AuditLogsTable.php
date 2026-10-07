<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("auditable_type")
                    ->label("Entity")
                    ->state(function($record){
                        return explode(
                            "\\",
                            $record->auditable_type
                        )[2];
                    })
                ,
                TextColumn::make("event")
                    ->label("Action")
                ,
                TextColumn::make("auditable_id")
                    ->label("Id")
                ,
                TextColumn::make("ip_address"),
                TextColumn::make("user.name")
                    ->state(function($record) {
                        if(!$record->user_id)
                        {
                            if($record->request_id)
                            {
                                return "Guest";
                            }

                            return "System";
                        }

                        return $record
                            ->user
                            ->name;
                    })
                ,
                TextColumn::make("created_at")
                    ->label("When")
                    ->state(fn($record) => $record
                        ->created_at
                        ->diffForHumans())
                ,
            ])
            ->filters([
                //
            ]);
    }
}
