<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\NameParserInterface;
use App\Http\Requests\StoreHomeOwnersRequest;
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
     * Delegates CSV file processing to the ParsingService and returns the result
     * as a JSON response.
     *
     * @param StoreHomeOwnersRequest $request Validated request containing CSV file
     * @param NameParserInterface $parser Service for parsing homeowner names
     * @return JsonResponse JSON response containing parsed homeowner data
     */
    public function __invoke(StoreHomeOwnersRequest $request, NameParserInterface $parser): JsonResponse
    {
        $file = $request->file('csv_file');

        if (!$file) {
            return response()->json([
                'success' => false,
                'error' => 'No CSV file provided'
            ], 400);
        }

        $result = $parser->parseCsvFile($file);

        return response()->json($result);
    }
}
