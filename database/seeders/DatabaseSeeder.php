<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()->create([
            'full_name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@user.com',
            'role' => 'admin',
        ]);

        // create operator user
        User::factory()->create([
            'full_name' => 'Operator User',
            'username' => 'operatoruser',
            'email' => 'operator@user.com',
            'role' => 'operator',
        ]);

        // create kepala_desa user
        User::factory()->create([
            'full_name' => 'Kepala Desa User',
            'username' => 'kepaladesauser',
            'email' => 'kepaladesa@user.com',
            'role' => 'kepala_desa',
        ]);

        // create staff user
        User::factory(4)->create();

        // create programs, projects, tasks, etc. using their respective seeders
        $this->call(ProgramSeeder::class);
        $this->call(OrganizationStructureSeeder::class);
    }
}
