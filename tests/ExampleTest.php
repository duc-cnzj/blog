<?php

class ExampleTest extends TestCase
{
    /** @test */
    public function example()
    {
        $res = $this->get('/');
        $year = date('Y');

        $this->assertContains(
            "created by duc@2018-{$year}.",
            $res->response->content()
        );
    }

    /** @test */
    public function response_has_function_timing()
    {
        $res = $this->get('/');

        $this->assertTrue($res->response->headers->has(config('duc.function_timing_key')));
    }
}
