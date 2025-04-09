<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Position;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем тестовую организацию
        $organization = Organization::create([
            'name' => 'Тестовая организация',
            'inn' => '1234567890',
            'kpp' => '123456789',
        ]);

        // Создаем тестовый отдел
        $department = Department::create([
            'name' => 'Тестовый отдел',
            'organization_id' => $organization->id,
        ]);

        // Создаем тестовую должность
        $position = Position::create([
            'name' => 'Тестовая должность',
            'department_id' => $department->id,
        ]);

        // Создаем администратора
        User::create([
            'name' => 'Администратор',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Создаем менеджера организации
        User::create([
            'name' => 'Менеджер организации',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'email_verified_at' => now(),
            'organization_id' => $organization->id,
        ]);

        // Создаем руководителя отдела
        User::create([
            'name' => 'Руководитель отдела',
            'email' => 'department@example.com',
            'password' => Hash::make('password'),
            'role' => 'department_head',
            'email_verified_at' => now(),
            'organization_id' => $organization->id,
            'department_id' => $department->id,
        ]);

        // Создаем специалиста по охране труда
        User::create([
            'name' => 'Специалист по ОТ',
            'email' => 'safety@example.com',
            'password' => Hash::make('password'),
            'role' => 'safety_specialist',
            'email_verified_at' => now(),
            'organization_id' => $organization->id,
        ]);

        // Создаем сотрудника
        User::create([
            'name' => 'Сотрудник',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'email_verified_at' => now(),
            'organization_id' => $organization->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
        ]);
    }
}
