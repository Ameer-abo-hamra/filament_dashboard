<?php

namespace App\Filament\Resources\CountryCodeResource\Pages;

use App\Filament\Resources\CountryCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCountryCodes extends ListRecords
{
    protected static string $resource = CountryCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
