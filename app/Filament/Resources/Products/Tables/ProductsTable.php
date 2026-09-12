<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('price')
                    ->money('INR', 100)
                    ->sortable(),
                TextColumn::make('description')->limit(5),
                TextColumn::make('category.name'),
                ImageColumn::make('image.filename')
                    ->label('Image')
                    ->imageHeight(40)
                    ->circular()
                    ->disk('upload')
                    ->alt('Product Image'),
                TextColumn::make('created_at')->since(),
                TextColumn::make('updated_at')->since(),
            ])
            ->filters([
                Filter::make('price')
                    ->schema([
                        TextInput::make('from')
                            ->numeric()
                            ->debounce(),
                        TextInput::make('to')
                            ->numeric()
                            ->debounce(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $from): Builder => $query->where('price', '>=', $from),
                                fn (Builder $query): Builder => $query
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $to): Builder => $query->where('price', '<=', $to),
                                fn (Builder $query): Builder => $query
                            );
                    }),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
