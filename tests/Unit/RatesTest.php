<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\FeeService;

class RatesTest extends TestCase
{
    public function testRate()
    {
        $file = fopen('data.csv', "r");
        $data = (new FeeService())->calculate($file);
        fclose($file);
        if(!$data || $data['code'] == 0){
            $this->assertFalse(true,'Can not Load Fee Service');
        }
        
        $this->assertTrue(true);
        
    }
}
