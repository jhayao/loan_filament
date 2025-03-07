<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        PhoneInput::make('phone')
                            ->required()
                            ->onlyCountries(['PH'])
                            ->initialCountry('PH'),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->rows(3),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->native(false)
                            ->placeholder('Select Company'),
                        Forms\Components\Select::make('branch_id')
                            ->relationship(
                                name: 'branch',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    $query->where('company_id', $get('company_id'));
                                }
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->native(false)
                            ->placeholder('Select Branch'),
                        Forms\Components\Select::make('area_id')
                            ->relationship(
                                name:'area',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    $query->where('company_id', $get('company_id'))->where('branch_id', $get('branch_id'));
                                }
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->native(false)
                            ->placeholder('Select Area'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('email'),
                PhoneColumn::make('phone'),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
