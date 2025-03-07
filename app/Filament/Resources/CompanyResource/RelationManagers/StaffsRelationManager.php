<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use App\Filament\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component as Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use function PHPUnit\Framework\throwException;

class StaffsRelationManager extends RelationManager
{
    protected static string $relationship = 'staffs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Staffs Information')
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
                            ->onlyCountries(['PH']),
                        TextInput::make('email')
                            ->required()
                            ->prefixIcon('heroicon-s-envelope'),
                        DatePicker::make('date_of_birth')
                            ->closeOnDateSelection()
                            ->prefixIcon('fas-cake-candles')
                            ->required()
                            ->native(false)
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->before(function (CreateAction $action,Staff $staff, array $data){
                        try{
                            $data['company_id'] = $this->getOwnerRecord()->id;
                            $is_email_exists = User::where('email', $data['email'])->first();
                            if($is_email_exists){
                                throw new \Exception('Email already exists');
                            }
                        }catch (\Exception $exception){
                                Notification::make()
                                    ->danger()
                                    ->title('Something went wrong')
                                    ->body($exception->getMessage())
                                    ->send();
                                $action->halt();
                        }
                        return $data;
                    })
                    ->after(function (CreateAction $action, Staff $staff, array $data){
                        $user['name'] =  $data['first_name'] . ' ' . $data['last_name'];
                        $user['email'] = $data['email'];
                        $user['password'] = Str::random(8);
                        $user = User::updateOrCreate($user);
                        $staff['user_id'] = $user->id;
                        $staff->save();
                    })


            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn(Model $record): string => StaffResource::getUrl('edit', ['record' => $record->getKey()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
