<?php

use App\Contracts\WhiteListUrlImp;

/**
 * Class WhiteListServiceTest
 */
class WhiteListUrlServiceTest extends TestCase
{
    /**
     * @var WhiteListUrlImp
     */
    protected $handler;

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->handler = app(WhiteListUrlImp::class);
    }

    /** @test */
    public function test_get_cache_key()
    {
        $this->assertEquals('testing_url_white_list', $this->handler->getCacheKey());
    }

    /** @test */
    public function can_not_add_same_key()
    {
        $this->handler->addItemToList('/user', '/user');
        $this->assertEquals(1, count($this->handler->getItemLists()));
    }

    /** @test */
    public function test_add_items()
    {
        $this->handler->addItemToList('/user', '/duc');
        $this->assertEquals(['/user', '/duc'], $this->handler->getItemLists());
    }

    /** @test */
    public function test_delete_item()
    {
        $this->handler->addItemToList('/user', '/duc');
        $this->assertEquals(['/user', '/duc'], $this->handler->getItemLists());
        $this->assertTrue($this->handler->deleteItems('/duc'));
        $this->assertEquals(['/user'], $this->handler->getItemLists());
    }
}
