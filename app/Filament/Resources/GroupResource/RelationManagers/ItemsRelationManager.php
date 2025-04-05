<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static bool $shouldShowHeaderActions = true;
    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        // الحصول على عدد العناصر المرتبطة بهذه العلاقة
        $count = $ownerRecord->items()->count();
        return 'Items count' . " ({$count})";
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('group.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('commission')
                    ->label('commission')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('coin.name')
                    ->label("coin name")
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('coin.symbol')
                    ->label("coin symbol")
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('Reason_rejection')
                    ->label("rejection reason")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label("brand name")
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('inserted_by')
                    ->label("inserted by")
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('adminUpdated.email')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_new')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                    Tables\Columns\TextColumn::make('status')
                        ->label('Status')
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            '0' => 'Pending',
                            '1' => 'Approved',
                            '2' => 'Rejected',
                            default => 'Unknown'
                        })
                ,
                Tables\Columns\IconColumn::make('star')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('sold')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('editable')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('locked')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('visitors')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function (Item $record): string {
                        if ($record->image === null) {
                            return asset('category/categories/ameer.jpg');
                        }
                        $path = str_replace('"', '', $record->image);
                        $path = str_replace('\/', '\\', $path);
                        return asset('item\\' . $path);
                    })
                    ->action(
                        Tables\Actions\Action::make('view')
                            ->modalHeading('View Image')
                            ->modalContent(fn(Item $record): HtmlString => new HtmlString('<img src="' . asset('item\\' . $record->image) . '" class="w-full">'))
                    ),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Pending',
                        1 => 'Accepted',
                        2 => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('group')
                    ->relationship('group', 'name'),
                Tables\Filters\Filter::make('commission')
                    ->form([
                        Forms\Components\TextInput::make('commission_from')->numeric(),
                        Forms\Components\TextInput::make('commission_to')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['commission_from'],
                                fn (Builder $query, $value): Builder => $query->where('commission', '>=', $value)
                            )
                            ->when(
                                $data['commission_to'],
                                fn (Builder $query, $value): Builder => $query->where('commission', '<=', $value)
                            );
                    }),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('coin')
                    ->relationship('coin', 'name'),
                Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
                Tables\Filters\Filter::make('price')
                    ->form([
                        Forms\Components\TextInput::make('price_from')->numeric(),
                        Forms\Components\TextInput::make('price_to')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $value): Builder => $query->where('price', '>=', $value)
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $value): Builder => $query->where('price', '<=', $value)
                            );
                    }),
                Tables\Filters\TernaryFilter::make('is_new'),
                Tables\Filters\TernaryFilter::make('star'),
                Tables\Filters\TernaryFilter::make('sold'),
                Tables\Filters\TernaryFilter::make('editable'),
                Tables\Filters\TernaryFilter::make('locked'),
                Tables\Filters\Filter::make('visitors')
                    ->form([
                        Forms\Components\TextInput::make('visitors_from')->numeric(),
                        Forms\Components\TextInput::make('visitors_to')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['visitors_from'],
                                fn (Builder $query, $value): Builder => $query->where('visitors', '>=', $value)
                            )
                            ->when(
                                $data['visitors_to'],
                                fn (Builder $query, $value): Builder => $query->where('visitors', '<=', $value)
                            );
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    })
            ])
            ->actions([
                Action::make('accept')
                ->label('accept item ')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (Item $record) => $record->status == 0 || $record->status == 2)
                ->action(action: function ($record) {
                    $record->status = 1;
                    $record->save();

                    Notification::make()
                        ->title('Item accepted successfully')
                        ->success()
                        ->send();
                })->button(),
                Action::make('reject')
                ->label('reject item ')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (Item $record) => $record->status == 0 || $record->status == 1)
                ->action(action: function ($record) {
                    $record->status = 2;
                    $record->save();
                    Notification::make()
                    ->title('Item rejected successfully')
                    ->success()
                    ->send();
                })->button(),
            ])

          ;
    }
}
