<?php

declare(strict_types=1);

namespace Tests;

use App\Services\ParsingService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test suite for the ParsingService class.
 */
class ParsingServiceTest extends BaseTestCase
{
    /**
     * Test parsing a basic single person name with title.
     */
    public function test_basic_name_parsing(): void
    {
        $parser = new ParsingService();

        $result = $parser->parseHomeowner("Mr John Smith");
        $this->assertEquals([[
            'title' => 'Mr',
            'first_name' => 'John',
            'initial' => null,
            'last_name' => 'Smith'
        ]], $result);
    }

    /**
     * Test parsing multiple people compound names.
     */
    #[DataProvider('multiplePersonDataProvider')]
    public function test_multiple_people_parsing(string $input, array $expected): void
    {
        $parser = new ParsingService();

        $result = $parser->parseHomeowner($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for multiple person parsing test cases.
     *
     * @return array<string, array{input: string, expected: array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}>}>
     */
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

    /**
     * Test that names missing required fields are skipped and logged.
     */
    public function test_missing_required_fields_skipped_and_logged(): void
    {
        $parser = new ParsingService();

        Log::shouldReceive('error')
            ->once()
            ->with('Name must contain a valid title');

        $result = $parser->parseHomeowner("John");

        $this->assertSame([], $result);
    }
}
