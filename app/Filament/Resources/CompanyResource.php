<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make()
                        ->schema([
                            TextInput::make('name')
                                ->columnSpan(2)
                                ->label('Company Name'),
                            TextInput::make('slug'),

                            TextInput::make('registration_number')
                                ->columnSpan(1)
                                ->label('Registration Number'),
                            TextInput::make('tax_id')
                                ->columnSpan(1)
                                ->label('Tax ID'),
                            PhoneInput::make('phone_number')
                                ->onlyCountries(['PH'])
                                ->unique(Company::class, 'phone_number', fn($record) => $record)
                                ->required(),
                            TextInput::make('email')
                                ->email(),
                            TextInput::make('website')
                                ->url()
                                ->suffixIcon('heroicon-m-globe-alt'),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'inactive' => 'Inactive',
                                    'active' => 'Active',
                                    'suspended' => 'Suspended',
                                ])
                                ->native(false)
                                ->default('active'),
                            Textarea::make('address')
                                ->rows(4)
                                ->columnSpan(2),
                        ])->columns(2),
                    Section::make('Company Logo')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('logo')
                                ->image()
                                ->avatar()
                                ->alignCenter()
                                ->rules(['image', 'max:1024'])
                                ->required(),
                        ])->grow(false),
                ])->columnSpan([
                    'sm' => 2,
                    'md' => 3,
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('registration_number'),
                PhoneColumn::make('tax_id'),
                TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BranchesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
