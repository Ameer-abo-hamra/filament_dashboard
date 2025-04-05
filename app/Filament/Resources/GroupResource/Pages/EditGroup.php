<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    public function getTitle(): string
    {
        return 'All items in this group'; // النص الجديد الذي تريد عرضه
    }
    protected function getFormActions(): array
    {
        return []; // إفراغ الأزرار
    }


}
