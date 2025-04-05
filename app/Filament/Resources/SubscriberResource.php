<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Filament\Resources\SubscriberResource\RelationManagers;
use App\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    public static function canCreate(): bool
    {
        return false;
    }
    public static function getNavigationBadge(): ?string
    {
        return (string) Subscriber::count();
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Select::make('is_verified')
    //                 ->label('Verification Status')
    //                 ->options([
    //                     0 => 'Not Verified',
    //                     1 => 'Verified'
    //                 ])
    //                 ->helperText('Change subscriber verification status')
    //                 ->inlineLabel()
    //                 ->native(false)
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")
                ->searchable()
,                Tables\Columns\TextColumn::make('inserted_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birthdate')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Male',
                        1 => 'Female',
                        2 => "unKnown"
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('country_code_id.country_name')
                    ->label('Country')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verification_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_verified')
                    ->label('Verified')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Rejected',
                        1 => 'Approved',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Inactive',
                        1 => 'Active'
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Email Verification Status')
                    ->options([
                        0 => 'Not Verified',
                        1 => 'Verified'
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '0' => 'Inactive',
                        '1' => 'Active'
                    ])
            ])
            ->actions([
                    Tables\Actions\Action::make('activate')
                ->label('Activate account')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (Subscriber $record) => $record->is_active == 0)
                ->action(function (Subscriber $record) {
                    $record->is_active = 1;
                    $record->save();
                    Notification::make()
                        ->title('Account activated successfully')
                        ->success()
                        ->send();
                })->button(),
                Tables\Actions\Action::make('deactivate')
                ->label('Deactivate account')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (Subscriber $record) => $record->is_active == 1)
                ->action(action: function ($record) {
                    $record->is_active = 0;
                    $record->save();
                    Notification::make()
                        ->title('Account deactivated successfully')
                        ->success()
                        ->send();
                })->button(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    ;}

    public static function getRelations(): array
    {
        return [
            RelationManagers\GroupsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            // 'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
