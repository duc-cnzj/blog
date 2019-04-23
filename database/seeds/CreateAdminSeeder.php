<?php

use App\User;
use Illuminate\Database\Seeder;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminMobile = env('ADMIN_MOBILE') ?: '123456789';
        $adminPwd = env('ADMIN_PWD');

        factory(User::class)->create([
            'name'     => 'admin',
            'mobile'   => $adminMobile,
            'password' => $adminPwd ?: 'secret',
        ]);

        $user = User::query()->where('mobile', $adminMobile)->first();

        $this->command->info('管理员创建成功！');
        $this->command->table(['id', 'name', 'mobile'], [
            [$user->id, $user->name, $user->mobile],
        ]);
    }
}
