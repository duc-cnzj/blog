<?php

use App\Tag;
use App\User;
use App\Article;
use App\Category;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class ArticleTableSeeder extends Seeder
{
    protected $tagNums = 6;
    protected $categoryNums = 6;
    protected $articleNums = 60;

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $adminMobile = env('ADMIN_MOBILE') ?: '123456789';

        $admin = User::query()->where('mobile', $adminMobile)->first();

        if ($admin) {
            $this->seedArticle($admin->id);
        } else {
            $this->command->warn('管理员还未创建，现在自动创建管理员！');

            $this->call(CreateAdminSeeder::class);
            $admin = User::query()->where('mobile', $adminMobile)->first();

            $this->seedArticle($admin->id);
        }
    }

    public function seedArticle(int $id): void
    {
        $this->command->info('开始填充文章数据！');

        factory(Category::class, $this->tagNums)->create(['user_id' => $id]);
        factory(Tag::class, $this->categoryNums)->create(['user_id' => $id]);
        factory(Article::class, $this->articleNums)->create([
            'author_id'   => $id,
            'category_id' => random_int(1, $this->categoryNums),
        ])->each(function (Article $item) {
            $item->tags()->sync(
                Arr::random(
                    range(1, $this->tagNums),
                    random_int(1, $this->tagNums)
                )
            );
        });

        $this->command->info('文章数据填充成功！');
    }
}
