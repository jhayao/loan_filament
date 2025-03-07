<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Customer Information')
                        ->icon('fas-user')
                        ->completedIcon('fas-user-check')
                        ->schema([
                            Select::make('company_id')
                                ->relationship(name: 'company', titleAttribute: 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->placeholder('Select Company'),
                            Select::make('branch_id')
                                ->relationship(
                                    name: 'branch',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query, Get $get) {
                                        $query->where('company_id', $get('company_id'));
                                    }
                                )
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->placeholder('Select Branch'),
                            Select::make('area_id')
                                ->relationship(name: 'area', titleAttribute: 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                    $query->where('company_id', $get('company_id'))
                                        ->where('branch_id', $get('branch_id'));
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->placeholder('Select Area'),
                            Select::make('customer_id')
                                ->relationship(name: 'customer', modifyQueryUsing: function (Builder $query, Get $get) {
                                    $query->where('company_id', $get('company_id'))
                                        ->where('branch_id', $get('branch_id'))
                                        ->where('area_id', $get('area_id'));
                                })
                                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->first_name} {$record->last_name}")
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->placeholder('Select Customer'),
                        ]),
                    Step::make('Loan Information')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            TextInput::make('amount')
                                ->placeholder('0.00')
                                ->required(),
                            TextInput::make('interest_rate')
                                ->placeholder('0.00')
                                ->hint('Interest rate was based on company settings')
                                ->required(),
                            Select::make('term')
                                ->native(false)
                                ->live()
                                ->hint('Loan term in months(30 Days)')
                                ->options([
                                    '1' => '1 Month',
                                    '2' => '2 Months',
                                    '3' => '3 Months',
                                    '4' => '4 Months',
                                    '5' => '5 Months',
                                    '6' => '6 Months',
                                    '7' => '7 Months',
                                    '8' => '8 Months',
                                    '9' => '9 Months',
                                    '10' => '10 Months',
                                    '11' => '11 Months',
                                    '12' => '12 Months',
                                ])
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $set('end_date', Carbon::parse($get('start_date'))->addDays(intval($get('term') * 30)));
                                })
                                ->required(),
                            DatePicker::make('start_date')
                                ->default(now())
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $set('end_date', Carbon::parse($get('start_date'))->addDays(intval($get('term') * 30)));
                                })
                                ->rules([
                                    fn(): Closure => function (string $attribute, $value, Closure $fail){
                                        if (Carbon::parse($value)->isSunday()) {
                                            $fail('Start date must not be on Sunday');
                                        }
                                    }
                                ])
                                ->native(false)
                                ->required(),
                            DatePicker::make('end_date')
                                ->default(fn(Get $get) => Carbon::parse($get('start_date'))->addDays(intval($get('term') * 30)))
                                ->native(false)
                                ->disabled()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'approved' => 'Approved',
                                    'pending' => 'Pending',
                                    'rejected' => 'Rejected',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('remarks')
                                ->rows(3),
                        ])
                ])->columnSpan(3)->skippable(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
