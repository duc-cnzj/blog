<?php

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
     */
    public function run()
    {
        if (App\Article::count() > 0) {
            return;
        }

        factory(App\Category::class, $this->tagNums)->create();
        factory(App\Tag::class, $this->categoryNums)->create();
        factory(App\Article::class, $this->articleNums)->create([
            'category_id' => random_int(1, $this->categoryNums)
        ])->each(function ($item) {
            $item->tags()->sync(
                array_random(
                    range(1, $this->tagNums),
                    random_int(1, $this->tagNums)
                )
            );
        });
    }
}
