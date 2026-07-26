<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Dimas',
            'email' => 'dimas@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $this->callWith(DemoSeeder::class, ['userId' => $user->id]);
    }
}
