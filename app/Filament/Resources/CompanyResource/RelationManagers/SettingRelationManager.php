<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Models\CompanySetting;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SettingRelationManager extends RelationManager
{
    protected static string $relationship = 'setting';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('interest_rate')
                    ->suffix('%')
                    ->inputMode('decimal')
                    ->rules(['digits_between:1,2'])
                    ->required(),
                TextInput::make('max_branch')
                    ->visible(fn () => Auth::user()->hasRole('super_admin'))
                    ->rules(['digits_between:1,2'])
                    ->required(),
                TextInput::make('max_area')
                    ->label('Max Area per branch')
                    ->rules(['digits_between:1,2'])
                    ->visible(fn () => Auth::user()->hasRole('super_admin'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        $company_settings = CompanySetting::where('company_id', $this->getOwnerRecord()->id);
        $shouldCreateSetting = $company_settings->count() === 1;

        return $table
            ->recordTitleAttribute('interest_rate')
            ->columns([
                Tables\Columns\TextColumn::make('interest_rate')->suffix('%'),
                Tables\Columns\TextColumn::make('max_branch'),
                Tables\Columns\TextColumn::make('max_area'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(!$shouldCreateSetting),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->recordTitle('Settings'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
