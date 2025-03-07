<?php

namespace App\Filament\Company\Resources\CompanySettingsResource\Pages;

use App\Filament\Company\Resources\CompanySettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySettings extends EditRecord
{
    protected static string $resource = CompanySettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
