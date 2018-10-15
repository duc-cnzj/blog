<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminMobile = env('ADMIN_MOBILE');
        $adminPwd = env('ADMIN_PWD');

        if (! User::whereMobile($adminMobile)->exists()) {
            factory(User::class)->create([
                'name' => 'admin',
                'mobile' => $adminMobile ?: '123456789',
                'password' => Hash::make($adminPwd ?: 'secret'),
            ]);
        }
        // $this->call('UsersTableSeeder');
    }
}
