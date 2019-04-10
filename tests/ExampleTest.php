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
}
