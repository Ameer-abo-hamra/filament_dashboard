<?php

namespace App\Filament\Resources\CountryCodeResource\Pages;

use App\Filament\Resources\CountryCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCountryCode extends EditRecord
{
    protected static string $resource = CountryCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
