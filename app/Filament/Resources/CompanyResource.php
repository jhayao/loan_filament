<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Filament\Resources\CompanyResource\RelationManagers\AreasRelationManager;
use App\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use App\Filament\Resources\CompanyResource\RelationManagers\StaffsRelationManager;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([

                    Forms\Components\Section::make('Company Details')
                        ->schema([
                            TextInput::make('name')
                                ->unique(Company::class, 'name',fn ($record) => $record)
                                ->required(),
                            TextInput::make('address')
                                ->required(),
                            PhoneInput::make('phone')
                                ->onlyCountries(['PH'])
                                ->unique(Company::class, 'phone',fn ($record) => $record)
                                ->required(),
                            TextInput::make('email')
                                ->required()
                                ->unique(Company::class, 'email',fn ($record) => $record)
                                ->prefixIcon('heroicon-s-envelope')
                                ->email(),
                        ])->grow(true),
                    Forms\Components\Section::make('Company Logo')
                        ->columns(2)
                        ->schema([
                            Forms\Components\FileUpload::make('avatar_url')
                                ->image()
                                ->avatar()
                                ->rules(['image', 'max:1024'])
                                ->required(),
                        ])->grow(false),
                ])->columnSpan(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('address'),
                PhoneColumn::make('phone'),
                TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make('Company Information')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label("Company Name"),
                            TextEntry::make('address')
                                ->label("Company Address"),
                            PhoneEntry::make('phone')
                                ->label('Company Phone Number'),
                            TextEntry::make('email')
                                ->label("Company Email Address"),
                            TextEntry::make('setting.interest_rate')
                                ->suffix('%')
                                ->label("Interest Rate"),
                        ]),
                    Section::make([
                        Section::make([
                            ImageEntry::make('avatar_url')
                                ->circular()
                                ->label('Company Logo'),
                        ]),
                        Section::make([
                            TextEntry::make('created_at')
                                ->label('Created At'),
                        ])
                    ])->grow(false)
                ])->columnSpan(3)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SettingRelationManager::class,
            BranchesRelationManager::class,
            StaffsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
            'view' => Pages\ViewCompany::route('/{record}'),
        ];
    }
}
