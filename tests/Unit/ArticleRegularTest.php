<?php

namespace Tests\Feature;

use App\User;
use TestCase;
use App\ArticleRegular;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleRegularTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_user()
    {
        $regular = create(ArticleRegular::class);

        $this->assertInstanceOf(User::class, $regular->user);
    }

    /** @test */
    public function each_will_set_rule()
    {
        $regular = create(ArticleRegular::class, ['rule' => ['express' => '^1\.', 'replace' => '##']]);

        $this->assertSame('/[↵]*1\./', $regular->rule['express']);
    }
}
