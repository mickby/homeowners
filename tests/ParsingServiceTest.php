<?php

namespace Tests;

use App\Services\ParsingService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

 class ParsingServiceTest extends BaseTestCase
{
    public function test_basic_name_parsing(): void
    {
        $parser = new ParsingService();

        $result = $parser->parseHomeowner("John Smith");
        $this->assertEquals([[
            'title' => '',
            'first_name' => 'John',
            'initial' => null,
            'last_name' => 'Smith'
        ]], $result);
    }

    public function test_multiple_people_parsing(): void
    {
        $parser = new ParsingService();

        $result = $parser->parseHomeowner("Mr and Mrs Smith");
        #should return two records, assert both
        $expected = [
            [
                'title' => 'Mr',
                'first_name' => null,
                'initial' => null,
                'last_name' => 'Smith'
            ],
            [
                'title' => 'Mrs',
                'first_name' => null,
                'initial' => null,
                'last_name' => 'Smith'
            ]
        ];
        $this->assertEquals($expected, $result);


    }


}
