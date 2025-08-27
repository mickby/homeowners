<?php

namespace App\Services;

class ParsingService
{
    public function parseHomeowner(string $name): array
    {
        // Check for compound names (multiple people)
        if (str_contains($name, ' and ') || str_contains($name, ' & ')) {
            return $this->parseCompoundName($name);
        }

        // Single person parsing
        return $this->parseSinglePerson($name);
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
        
        // For "Mr and Mrs Smith", the clean name should be just "Smith"
        $lastName = $cleanName;
        
        // Create person records for each title
        foreach ($titles as $title) {
            $people[] = [
                'title' => $title,
                'first_name' => null,
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
