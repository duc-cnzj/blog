<?php

use App\Tag;
use App\User;
use App\Article;
use App\Category;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

/**
 * Class ArticleTableSeeder
 */
class ArticleTableSeeder extends Seeder
{
    /**
     * @var int
     */
    protected $tagNums = 6;
    /**
     * @var int
     */
    protected $categoryNums = 6;
    /**
     * @var int
     */
    protected $articleNums = 60;

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $admin = User::query()->find(1);

        if ($admin) {
            $this->seedArticle(1);
        } else {
            $this->command->warn('管理员还未创建，现在自动创建管理员！');

            $this->call(CreateAdminSeeder::class);

            $this->seedArticle(1);
        }
    }

    /**
     * @param  int  $id
     * @throws Exception
     *
     * @author duc <1025434218@qq.com>
     */
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
