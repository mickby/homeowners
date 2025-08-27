<?php

namespace Tests;

use App\Services\ParsingService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

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

    #[DataProvider('multiplePersonDataProvider')]
    public function test_multiple_people_parsing(string $input, array $expected): void
    {
        $parser = new ParsingService();

        $result = $parser->parseHomeowner($input);
        $this->assertEquals($expected, $result);
    }

    public static function multiplePersonDataProvider(): array
    {
        return [
            'Mr and Mrs Smith' => [
                'input' => 'Mr and Mrs Smith',
                'expected' => [
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
                ]
            ],
            'Dr & Mrs Joe Bloggs' => [
                'input' => 'Dr & Mrs Joe Bloggs',
                'expected' => [
                    [
                        'title' => 'Dr',
                        'first_name' => 'Joe',
                        'initial' => null,
                        'last_name' => 'Bloggs'
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Joe',
                        'initial' => null,
                        'last_name' => 'Bloggs'
                    ]
                ]
            ]
        ];
    }


}
