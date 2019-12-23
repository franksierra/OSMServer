<?php

use App\Models\App;
use App\Models\AppKey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Users
        $user = User::where('username', 'admin')->first();
        $app = App::create([
            'name' => 'TestKey',
            'user_id' => $user->id,
            'alias' => 'testkey',
            'created_at' => Carbon::now()
        ]);
        $app_key = AppKey::create([
            'app_id' => $user->id,
            'platform' => 'other',
            'key' => 'testkey123456',
            'active' => 1,
            'created_at' => Carbon::now()
        ]);
    }
}
