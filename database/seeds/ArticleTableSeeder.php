<?php

use App\Tag;
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
        factory(Category::class, $this->tagNums)->create();
        factory(Tag::class, $this->categoryNums)->create();
        factory(Article::class, $this->articleNums)->create([
            'category_id' => random_int(1, $this->categoryNums),
        ])->each(function (Article $item) {
            $item->tags()->sync(
                Arr::random(
                    range(1, $this->tagNums),
                    random_int(1, $this->tagNums)
                )
            );
        });
    }
}
