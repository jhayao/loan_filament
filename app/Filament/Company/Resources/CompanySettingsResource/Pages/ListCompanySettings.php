<?php

namespace App\Filament\Company\Resources\CompanySettingsResource\Pages;

use App\Filament\Company\Resources\CompanySettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanySettings extends ListRecords
{
    protected static string $resource = CompanySettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
