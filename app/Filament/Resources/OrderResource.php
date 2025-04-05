<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    public static function getNavigationBadge(): ?string
    {
        return (string) Order::count();
    }

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('sub_id')
                    ->label('Subscriber name')
                    ->relationship('sub', titleAttribute: 'full_name')
                    ->disabled(),
                TextInput::make('total')
                    ->numeric()
                    ->disabled(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'Approved',
                        '2' => 'Rejected',
                    ])
                    ->required(),

                TextInput::make('order_date')
                    ->type('datetime-local')
                    ->disabled(),
                TextInput::make('created_at')
                    ->type('datetime-local')
                    ->disabled(),
                TextInput::make('updated_at')
                    ->type('datetime-local')
                    ->disabled(),
                TextInput::make('deleted_at')
                    ->type('datetime-local')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cart_id')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sub.full_name')
                    ->label('Subscriber Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '0' => 'Pending',
                        '1' => 'Approved',
                        '2' => 'Rejected',
                        default => 'Unknown'
                    })
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Items Count') // تسمية الحقل
                    ->getStateUsing(fn(Order $record) => $record->items()->count()) // حساب العدد
                    ->searchable(), // إتاحة البحث

                TextColumn::make('adminUpdated.name'),
                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                Tables\Filters\Filter::make('subscriber')
                    ->form([
                        Forms\Components\TextInput::make('subscriber_name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['subscriber_name'],
                            fn(Builder $query, $value): Builder => $query->whereHas(
                                'sub',
                                fn($query) =>
                                $query->where('full_name', 'like', "%{$value}%")
                            )
                        );
                    }),
                Tables\Filters\Filter::make('total')
                    ->form([
                        Forms\Components\TextInput::make('total_from')->numeric(),
                        Forms\Components\TextInput::make('total_to')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['total_from'],
                                fn(Builder $query, $value): Builder => $query->where('total', '>=', $value)
                            )
                            ->when(
                                $data['total_to'],
                                fn(Builder $query, $value): Builder => $query->where('total', '<=', $value)
                            );
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'Approved',
                        '2' => 'Rejected',
                    ]),
                Tables\Filters\Filter::make('items')
                    ->form([
                        Forms\Components\TextInput::make('item_name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['item_name'],
                            fn(Builder $query, $value): Builder => $query->whereHas(
                                'items',
                                fn($query) =>
                                $query->where('name', 'like', "%{$value}%")
                            )
                        );
                    }),
                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('order_from'),
                        Forms\Components\DatePicker::make('order_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['order_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date)
                            )
                            ->when(
                                $data['order_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date)
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Action::make('accept')
                    ->label('accept order ')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(Order $record) => $record->status == 0 || $record->status == 2)
                    ->action(action: function ($record) {

                        $record->status = 1;
                        $record->save();

                        Notification::make()
                            ->title('Order accepted successfully')
                            ->success()
                            ->send();
                    })->button(),
                Action::make('reject')
                    ->label('reject order ')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(Order $record) => $record->status == 0 || $record->status == 1)
                    ->action(action: function ($record) {

                        $record->status = 2;
                        $record->save();
                        Notification::make()
                            ->title('Order rejected successfully')
                            ->success()
                            ->send();
                    })->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
