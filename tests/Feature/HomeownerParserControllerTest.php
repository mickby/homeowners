<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\ParsingServiceTest;

class HomeownerParserControllerTest extends ParsingServiceTest
{
    public function test_parse_route_returns_correct_array(): void
    {
    $csvContent = "homeowner,
        Mr John Smith,
        Mrs Jane Smith";

        $file = UploadedFile::fake()
            ->createWithContent('homeowners.csv', $csvContent);

        $response = $this->post(route('parse'), [
            'csv_file' => $file
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'count' => 2,
            'homeowners' => [
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'initial' => null,
                    'last_name' => 'Smith'
                ],
                [
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'initial' => null,
                    'last_name' => 'Smith'
                ]
            ]
        ]);
    }

}

