<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ParsingService
{
    public function parseHomeowner(string $name): array
    {
        try {
            $this->validateRecord($name);
        } catch (InvalidArgumentException $e){
          Log::error($e->getMessage()); # log and continue
          return [];
        }

        // Check for compound names (multiple people)
        if (str_contains($name, ' and ') || str_contains($name, ' & ')) {
            return $this->parseCompoundName($name);
        }

        // Single person parsing
        return $this->parseSinglePerson($name);
    }

    private function validateRecord(string $name): void
    {
        #title and last name are required

        if (empty(trim($name))) {
            throw new InvalidArgumentException("Name cannot be empty");
        }

        if (!preg_match('/(Mr|Mrs|Ms|Dr|Prof|Mister)/i', $name)) {
            throw new InvalidArgumentException("Name must contain a valid title");
        }

        $parts = preg_split('/\s+/', trim($name));

        if (count($parts) < 2) {
            throw new InvalidArgumentException("Name must contain at least two words");
        }




    }

    private function parseCompoundName(string $name): array
    {
        $people = [];

        // Extract all titles
        preg_match_all('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/', $name, $matches);
        $titles = $matches[1] ?? [];

        // Remove titles first, then remove "and" or "&"
        $cleanName = preg_replace('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/i', '', $name);
        $cleanName = preg_replace('/(and|&)\s+/', '', $cleanName);
        $cleanName = trim($cleanName);

        // Parse the remaining name parts
        $nameParts = explode(' ', $cleanName);

        if (count($nameParts) === 1) {
            // Only last name (e.g., "Mr and Mrs Smith")
            $firstName = null;
            $lastName = $nameParts[0];
        } else {
            // First name and last name (e.g., "Dr & Mrs Joe Bloggs")
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];
        }

        // Create person records for each title
        foreach ($titles as $title) {
            $people[] = [
                'title' => $title,
                'first_name' => $firstName,
                'initial' => null,
                'last_name' => $lastName
            ];
        }

        return $people;
    }

    private function parseSinglePerson(string $name): array
    {
        // Extract title
        $title = "";
        if (preg_match('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/', $name, $matches)) {
            $title = $matches[1];
        }

        // Extract names without titles
        $cleanName = preg_replace('/(Mr|Mrs|Ms|Dr|Prof|Mister)\.?\s+/i', '', $name);
        $cleanName = trim($cleanName);
        $parts = explode(' ', trim($cleanName, ','));

        return [[
            'title' => $title,
            'first_name' => count($parts) > 1 ? $parts[0] : null,
            'initial' => null,
            'last_name' => count($parts) > 1 ? $parts[1] : $parts[0]
        ]];
    }
}
