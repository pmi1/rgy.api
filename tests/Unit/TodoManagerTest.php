<?php

namespace Tests\Unit;

use App\TodoManager;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoManagerTest extends TestCase
{
    private $todoManager;

    public function setUp()
    {
        $this->todoManager = new TodoManager;

        foreach ($this->additionProvider() as $item) {
            $val = $item[0];
            $this->todoManager->$val = 1;
        }
    }

    /**
     * @param $attr
     * @dataProvider additionProvider
     */
    public function testHasAttribute($attr, $expected)
    {
        $this->assertEquals($expected, $this->todoManager->hasAttribute($attr));
    }

    public function additionProvider()
    {
        return [
            ['typeId', true],
            ['statusId', true],
            ['Invalid', true],
        ];
    }

}
