<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeOwnersRequest;
use App\Services\ParsingService;


class HomeownerParserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreHomeOwnersRequest $request, ParsingService $parser)
    {

        $file = $request['csv_file'];
        $handle = fopen($file->getPathname(), 'r');

        fgetcsv($handle); // get rid of the header

        $homeowners = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (!empty(trim($row[0]))) {
                $homeowners = array_merge($homeowners, $parser->parseHomeowner($row[0]));
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'count' => count($homeowners),
            'homeowners' => $homeowners
        ]);
    }


}
