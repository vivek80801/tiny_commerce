<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name"),
                TextColumn::make("email"),
                ToggleColumn::make("is_admin")
                    ->label("Admin")
                ,
                TextColumn::make("created_at")->label("Created")->since(),
                TextColumn::make("updated_at")->label("Updated")->since(),
            ])
            ->filters([
                Filter::make("admin")
                    ->query(fn (Builder $query): Builder => $query->where("is_admin", "=", true)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn ($record) => $record->is(auth()->user()))
                ,
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records, DeleteBulkAction $action){
                            if (
                                $records->contains(
                                    fn ($record) => $record->is(auth()->user())
                                )
                            )
                            {
                                $action->cancel();
                            }
                        })
                    ,
                ]),
            ]);
    }
}
