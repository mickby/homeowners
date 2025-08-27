<?php

namespace App\Services;

class ParsingService
{
    public function parseHomeowner(string $name): array
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


        $parsed = [[
            'title' => $title,
            'first_name' => $parts[0],
            'initial' => null,
            'last_name' => $parts[1],
        ]];

        return $parsed;
    }

}
