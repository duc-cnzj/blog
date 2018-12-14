<?php


class ExampleTest extends TestCase
{
    /** @test */
    public function example()
    {
        $res = $this->get('/');
        $this->assertStringContainsStringIgnoringCase(
            'created by duc@2018.',
            $res->response->content()
        );
    }
}
