<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'is_admin' => true,
            'status' => 'VALIDATED',
        ]);

        User::factory()->create([
            'is_admin' => false,
            'status' => 'VALIDATED',
        ]);

        $this->call([
            TeamSeeder::class,
            ParticipationSeeder::class,
            PhaseSeeder::class,
            EventSeeder::class,
            BetSeeder::class,
            BettorSeeder::class,
        ]);
    }
}
