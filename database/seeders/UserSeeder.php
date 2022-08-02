<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'is_admin' => true,
            'status' => 'VALIDATED',
        ]);

        User::factory()->count(10)->create([
            'is_admin' => false,
        ]);
    }
}
