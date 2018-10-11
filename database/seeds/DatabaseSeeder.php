<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! User::whereMobile('18888780080')->exists()) {
            factory(User::class)->create([
                'name' => 'duc',
                'mobile' => '18888780080'
            ]);
        }
        // $this->call('UsersTableSeeder');
    }
}
