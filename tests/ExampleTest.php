<?php


class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $res = $this->get('/');
        $this->assertStringContainsStringIgnoringCase(
            'created by duc@2018.',
            $res->response->content()
        );
    }
}
