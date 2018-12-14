<?php


class ExampleTest extends TestCase
{
    /** @test */
    public function example()
    {
        $res = $this->get('/');
        $this->assertContains(
            'created by duc@2018.',
            $res->response->content()
        );
    }
}
