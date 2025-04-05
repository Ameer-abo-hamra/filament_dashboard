<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;
    public function getTitle(): string
    {
        return 'All groups belong to this subscriber'; // النص الجديد الذي تريد عرضه
    }
    protected function getFormActions(): array
    {
        return []; // إفراغ الأزرار
    }
    protected function getTabs(): array
    {
        return [
            'groups' => [
                'label' => 'المجموعات (' . $this->record->groups()->count() . ')',
                'icon' => 'heroicon-o-users', // اختياري
            ],
        ];
    }
}
