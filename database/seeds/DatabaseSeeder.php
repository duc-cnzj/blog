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
        $adminMobile = env('ADMIN_MOBILE', '123456789');
        $adminPwd = env('ADMIN_PWD', 'secret');

        if (! User::whereMobile($adminMobile)->exists()) {
            factory(User::class)->create([
                'name' => 'admin',
                'mobile' => $adminMobile,
                'password' => bcrypt($adminPwd),
            ]);
        }
        // $this->call('UsersTableSeeder');
    }
}
