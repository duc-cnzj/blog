<?php

use App\Services\IpRedisCacheStore;

/**
 * Class IpRedisCacheTest
 */
class IpRedisCacheTest extends TestCase
{
    /**
     * @var IpRedisCacheStore
     */
    protected $handler;

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->handler = new IpRedisCacheStore();
    }

    /** @test */
    public function test_put_and_get()
    {
        $this->handler->put('duc', 'value');

        $this->assertEquals('value', $this->handler->get('duc'));

        $this->handler->put('a', ['a', 'b']);
        $this->assertEquals(['a', 'b'], $this->handler->get('a'));
    }

    /** @test */
    public function cache_store_test_forget()
    {
        $this->handler->put('duc', 'cool');
        $this->assertEquals('cool', $this->handler->get('duc'));
        $this->handler->forget('duc');
        $this->assertNull($this->handler->get('duc'));
    }

    /** @test */
    public function cache_store_test_flush()
    {
        $this->handler->put('duc', 'cool');
        $this->handler->put('abc', 'cool');
        $this->handler->put('bbc', 'cool');
        $this->assertCount(3, $this->handler);
        $this->handler->flush();
        $this->assertCount(0, $this->handler);
    }

    /** @test */
    public function cache_store_test_get_prefix()
    {
        $this->assertEquals('', $this->handler->getPrefix());
    }

    /** @test */
    public function cache_store_test_many()
    {
        $this->assertEquals([null, null], $this->handler->many(['duc', 'abc']));
    }

    /** @test */
    public function test_put_many()
    {
        $this->handler->putMany(['name'=>'duc', 'age' => 18]);
        $this->assertCount(2, $this->handler);
    }

    /** @test */
    public function test_get_all_items()
    {
        $this->handler->putMany(['name'=>'duc', 'age' => 18]);
        $this->assertEquals(['name'=>'duc', 'age' => 18], $this->handler->getAllItems());
    }
}
