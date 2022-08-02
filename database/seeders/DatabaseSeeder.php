<?php

namespace Database\Seeders;

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
        $this->call([
            UserSeeder::class,
            TeamSeeder::class,
            ParticipationSeeder::class,
            PhaseSeeder::class,
            EventSeeder::class,
            BetSeeder::class,
            BettorSeeder::class,
        ]);
    }
}
