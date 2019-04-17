<?php

use App\Contracts\WhiteListIpImp;
use App\Contracts\WhiteListUrlImp;

/**
 * Class WhiteListServiceTest
 */
class WhiteListIpServiceTest extends TestCase
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
        $this->handler = app(WhiteListIpImp::class);
    }

    /** @test */
    public function test_get_cache_key()
    {
        $this->assertEquals('testing_ip_white_list', $this->handler->getCacheKey());
    }

    /** @test */
    public function can_not_add_same_key()
    {
        $this->handler->addItemToList('192.168.1.1', '192.168.1.1');
        $this->assertEquals(1, count($this->handler->getItemLists()));
    }

    /** @test */
    public function test_add_items()
    {
        $this->handler->addItemToList('192.168.1.1', '192.168.1.2');
        $this->assertEquals(['192.168.1.1', '192.168.1.2'], $this->handler->getItemLists());
    }

    /** @test */
    public function test_delete_item()
    {
        $this->handler->addItemToList('192.168.1.1', '192.168.1.2');
        $this->assertEquals(['192.168.1.1', '192.168.1.2'], $this->handler->getItemLists());
        $this->assertTrue($this->handler->deleteItems('192.168.1.2'));
        $this->assertEquals(['192.168.1.1'], $this->handler->getItemLists());
    }
}
