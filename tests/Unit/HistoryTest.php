<?php


use Laravel\Lumen\Testing\DatabaseMigrations;

class HistoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_user()
    {
        $history = factory(\App\History::class)->state('withAdminUser')->create();

        $this->assertInstanceOf(\App\User::class, $history->userable);
    }
}