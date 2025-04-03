<?php

namespace App\Filament\Resources\AiClientResource\Pages;

use App\Filament\Resources\AiClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiClient extends EditRecord
{
    protected static string $resource = AiClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
