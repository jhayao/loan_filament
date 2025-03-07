<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();
        try {
            $user =  User::create([
                'name' => $data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Str::random(8),
            ]);
            $user->roles()->attach($data['role']);
            $data['user_id'] = $user->id;
            $record = parent::handleRecordCreation($data);
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
