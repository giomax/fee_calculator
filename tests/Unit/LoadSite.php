<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LoadSite extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function loadTest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
