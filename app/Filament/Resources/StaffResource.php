<?php

namespace App\Filament\Resources;


use App\Filament\Resources\AreaResource\Pages\ViewArea;
use App\Filament\Resources\BranchResource\Pages\ViewBranch;
use App\Filament\Resources\StaffResource\Pages;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Staff;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Role;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Staffs Information')
                        ->schema([
                            TextInput::make('first_name')
                                ->required()
                                ->prefixIcon('fas-id-card')
                                ->maxLength(255),
                            TextInput::make('middle_name')
                                ->required()
                                ->prefixIcon('fas-id-card')
                                ->maxLength(255),
                            TextInput::make('last_name')
                                ->required()
                                ->prefixIcon('fas-id-card')
                                ->maxLength(255),
                            ButtonGroup::make('gender')
                                ->required()
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                ])
                                ->icons([
                                    'male' => 'fas-male',
                                    'female' => 'fas-female',
                                ])
                                ->onColor('primary')
                                ->offColor('gray')
                                ->gridDirection('row')
                                ->default('male'),
                            PhoneInput::make('phone_number')
                                ->required()
                                ->unique('staff', 'phone_number', fn($record) => $record)
                                ->onlyCountries(['PH']),
                            TextInput::make('email')
                                ->required()
                                ->email()
                                ->unique(
                                    table: 'users',
                                    column: 'email',
                                    modifyRuleUsing: fn(Unique $rule, $record) => $record ? $rule->ignore($record->user_id) : $rule
                                )
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('email_account', $state))
                                ->prefixIcon('heroicon-s-envelope'),
                            DatePicker::make('date_of_birth')
                                ->closeOnDateSelection()
                                ->prefixIcon('fas-cake-candles')
                                ->required()
                                ->native(false)
                        ]),
                    Step::make('Account Information')
                        ->visible(fn($record) => !$record)
                        ->schema([
                            TextInput::make('email_account')
                                ->label('Email Account')
                                ->required()
                                ->email()
                                ->live(onBlur: true),
                            TextInput::make('password')
                                ->label('Password')
                                ->required()
                                ->password()
                                ->minLength(8)
                                ->live()
                        ]),

                    Step::make('Role Information')
//                        ->visible(fn($record) => !$record)
                        ->schema([
                            MorphToSelect::make('assignable')
                                ->label('Assign to')
                                ->live()
                                ->types(
                                    [
                                        MorphToSelect\Type::make(Branch::class)
                                            ->getOptionLabelFromRecordUsing(function (Branch $branch) {
                                                return $branch->company->name . ' - ' . $branch->name;
                                            }),
                                        MorphToSelect\Type::make(Area::class)
                                            ->getOptionLabelFromRecordUsing(fn(Area $area) => $area->company->name . ' - ' . $area->branch->name . ' - ' . $area->name)
                                    ]
                                )
                                ->preload()
                                ->searchable()
                                ->native(false),
//
                            Select::make('role')
                                ->label('Role')
                                ->live()
                                ->relationship(
                                    name: 'role',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn(Builder $query) => $query->whereIn('assignable_type', [Branch::class, Area::class])->orderBy('id')
                                )
                                ->rules([
                                    fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                        $role = Role::find($value);
                                        if ($role->assignable_type != $get('assignable_type')) {
                                            $assignable_type = $get('assignable_type');
                                            $model = new $assignable_type();
                                            $className = class_basename($model);
                                            $fail("The role is not suitable for {$className}");
                                        }
                                    },
                                ])
                                ->disableOptionWhen(function (string $value, Get $get): bool {
                                    $role = Role::find($value);


                                    return $role->assignable_type != $get('assignable_type');
                                })
                                ->native(false)


                        ])
                ])->columnSpan(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Full Name'),
                TextColumn::make('email'),
                TextColumn::make('assignable.name')
                    ->url(function ($record): string {
                        if ($record->assignable_type == Branch::class) {
                            return ViewBranch::getUrl(['record' => $record->assignable->id]);
                        } else if ($record->assignable_type == Area::class) {
                            return ViewArea::getUrl(['record' => $record->assignable->id]);
                        } else {
                            return '#';
                        }
                    })
                    ->label('Assign to'),
                TextColumn::make('role.name')
                    ->label('Role'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
                    Section::make('Staff Information')
                        ->columns(3)
                        ->headerActions([
                            Action::make('edit')
                                ->label('Edit')
                                ->icon('heroicon-s-pencil-square')
                                ->url(fn($record) => Pages\EditStaff::getUrl(['record' => $record->id]))
                        ])
                        ->schema([
                            TextEntry::make('full_name')
                                ->label('Full Name'),
                            TextEntry::make('email'),
                            TextEntry::make('gender')
                                ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                                ->label('Gender'),
                            PhoneEntry::make('phone_number')
                                ->label('Phone Number'),
                            TextEntry::make('date_of_birth')
                                ->date()
                                ->label('Date of Birth'),

                        ]),
                ])->columnSpan(3),
                Split::make([
                    Section::make('Account Information')
                        ->columns(3)
                        ->headerActions([
                            Action::make('edit')
                                ->label('Modify')
                                ->icon('heroicon-s-pencil-square')
                                ->action(function ($data,$record) {
                                    $record->assignable_type = $data['assignable_type'];
                                    $record->assignable_id = $data['assignable_id'];
                                    $record->role_id = $data['role'];
                                    $record->save();
                                })
                                ->form([
                                    MorphToSelect::make('assignable')
                                        ->label('Assign to')
                                        ->live()
                                        ->types(
                                            [
                                                MorphToSelect\Type::make(Branch::class)
                                                    ->getOptionLabelFromRecordUsing(function (Branch $branch) {
                                                        return $branch->company->name . ' - ' . $branch->name;
                                                    }),
                                                MorphToSelect\Type::make(Area::class)
                                                    ->getOptionLabelFromRecordUsing(fn(Area $area) => $area->company->name . ' - ' . $area->branch->name . ' - ' . $area->name)
                                            ]
                                        )
                                        ->preload()
                                        ->searchable()
                                        ->native(false),
                                    Select::make('role')
                                        ->label('Role')
                                        ->live()
                                        ->relationship(
                                            name: 'role',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn(Builder $query) => $query->whereIn('assignable_type', [Branch::class, Area::class])->orderBy('id')
                                        )
                                        ->rules([
                                            fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                $role = Role::find($value);
                                                if ($role->assignable_type != $get('assignable_type')) {
                                                    $assignable_type = $get('assignable_type');
                                                    $model = new $assignable_type();
                                                    $className = class_basename($model);
                                                    $fail("The role is not suitable for {$className}");
                                                }
                                            },
                                        ])
                                        ->disableOptionWhen(function (string $value, Get $get): bool {
                                            $role = Role::find($value);
                                            return $role->assignable_type != $get('assignable_type');
                                        })
                                        ->native(false)
                                ])
                        ])
                        ->schema([
                            TextEntry::make('assignable.name')
                                ->suffixAction(
                                    Action::make('view')
                                        ->label('')
                                        ->icon('heroicon-o-arrow-right-circle')
                                        ->url(fn($record) => $record->assignable_type == Branch::class ? ViewBranch::getUrl(['record' => $record->assignable->id]) : ViewArea::getUrl(['record' => $record->assignable->id]))
                                )
                                ->label('Assign to'),
                            TextEntry::make('role.name')
                                ->label('Role'),
                        ]),
                ])->columnSpan(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            RelationManagers\AssignableRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
            'view' => Pages\ViewStaff::route('/{record}'),
        ];
    }


}
