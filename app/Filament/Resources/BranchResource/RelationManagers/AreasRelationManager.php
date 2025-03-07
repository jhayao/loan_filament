<?php

namespace App\Filament\Resources\BranchResource\RelationManagers;

use App\Filament\Resources\AreaResource;
use App\Models\Area;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AreasRelationManager extends RelationManager
{
    protected static string $relationship = 'areas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Area Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['company_id'] = $this->getOwnerRecord()->company_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->url(fn (Area $area): string => AreaResource::getUrl('view', ['record' => $area->getKey()])),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->url(fn (Area $area): string => AreaResource::getUrl('edit', ['record' => $area->getKey()])),
                Tables\Actions\DeleteAction::make()->label('')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
