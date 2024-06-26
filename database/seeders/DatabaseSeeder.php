<?php

namespace Database\Seeders;

use App\Models\FullScore;
use App\Models\Part;
use App\Models\SheetMusicItem;
use App\Models\SheetMusicList;
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
        /*User::factory(30)->create();

        FullScore::factory(50)->create();
        Part::factory(1500)->create();

        SheetMusicList::factory(10)->create();
        SheetMusicItem::factory(100)->create();*/

        User::factory()->create([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'bryan.sosof@gmail.com',
            'date_of_birth' => null,
            'phone_number' => null,
            'status' => true,
            'roles' => 'super-admin',
            'instrument_id' => null,
            'voice_id' => null,
        ]);
    }
}
