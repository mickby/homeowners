<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeOwnersRequest;


class HomeownerParserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreHomeOwnersRequest $request)
    {

        $file = $request['csv_file'];
        $handle = fopen($file->getPathname(), 'r');

        fgetcsv($handle); // get rid of the header

        $homeowners = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (!empty(trim($row[0]))) {
                $homeowners[] = $this->parseHomeowner(trim($row[0]));
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'count' => count($homeowners),
            'homeowners' => $homeowners
        ]);
    }

    private function parseHomeowner(string $name): array
    {
        // Extract title
        $title = "";
        if (preg_match_all('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/', $name, $matches)) {
            $title = $matches[1][0];
        }


        // Extract names without titles
        $cleanName = preg_replace('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/i', '', $name);
        $cleanName = trim($cleanName);
        $parts = explode(' ', trim($cleanName, ','));


         $parsed = [
                    'title' => $title,
                    'first_name' => $parts[0],
                    'initial' => null,
                    'last_name' => $parts[1],
                ];

            return $parsed;
    }
}
