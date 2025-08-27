<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeOwnersRequest;
use App\Services\ParsingService;
use Illuminate\Http\JsonResponse;

/**
 * Controller for parsing homeowner CSV files.
 *
 * Handles the processing of uploaded CSV files containing homeowner information,
 * parsing each record and returning structured data.
 */
class HomeownerParserController extends Controller
{
    /**
     * Parse homeowner data from uploaded CSV file.
     *
     * Processes an uploaded CSV file containing homeowner records, parses each entry
     * using the ParsingService, and returns a JSON response with all parsed homeowners.
     *
     * @param StoreHomeOwnersRequest $request Validated request containing CSV file
     * @param ParsingService $parser Service for parsing homeowner names
     * @return JsonResponse JSON response containing parsed homeowner data
     */
    public function __invoke(StoreHomeOwnersRequest $request, ParsingService $parser): JsonResponse
    {
        $file = $request->file('csv_file');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid CSV file upload'
            ], 400);
        }

        $homeowners = [];

        $handle = fopen($file->getPathname(), 'r');
        if ($handle === false) {
            return response()->json([
                'success' => false,
                'error' => 'Unable to read CSV file'
            ], 400);
        }

        try {
            // Skip the header row
            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $name = trim($row[0] ?? '');
                if ($name !== '') {
                    array_push($homeowners, ...$parser->parseHomeowner($name));
                }
            }
        } finally {
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'count' => count($homeowners),
            'homeowners' => $homeowners
        ]);
    }
}
