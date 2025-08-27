<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Service for parsing homeowner names from various formats.
 *
 * Handles parsing of homeowner names including single persons, compound names
 * (multiple people), and various title formats. Validates input and logs errors
 * for invalid records.
 */
class ParsingService
{
    /**
     * Valid title patterns for homeowner names.
     */
    private const VALID_TITLES = 'Mr|Mrs|Ms|Dr|Prof|Mister';

    /**
     * Parse homeowner names from a CSV file upload.
     *
     * Reads the uploaded CSV file, validates it, and processes each homeowner
     * record to extract structured person data.
     *
     * @param UploadedFile $file The uploaded CSV file
     * @return array{success: bool, count?: int, homeowners?: array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}>, error?: string} Result array
     */
    public function parseCsvFile(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            return [
                'success' => false,
                'error' => 'Invalid CSV file upload'
            ];
        }

        $homeowners = [];
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return [
                'success' => false,
                'error' => 'Unable to read CSV file'
            ];
        }

        try {
            // Skip the header row
            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $name = trim($row[0] ?? '');
                if ($name !== '') {
                    array_push($homeowners, ...$this->parseHomeowner($name));
                }
            }
        } finally {
            fclose($handle);
        }

        return [
            'success' => true,
            'count' => count($homeowners),
            'homeowners' => $homeowners
        ];
    }
    /**
     * Parse a homeowner name string into structured data.
     *
     * Processes homeowner names in various formats including single persons
     * and compound names (e.g., "Mr and Mrs Smith"). Returns structured data
     * with separate records for each person found.
     *
     * @param string $name The homeowner name to parse
     * @return array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}> Array of parsed person records
     */
    public function parseHomeowner(string $name): array
    {
        try {
            $this->validateRecord($name);
        } catch (InvalidArgumentException $e) {
            Log::error($e->getMessage());
            return [];
        }

        // Check for compound names (multiple people)
        if (str_contains($name, ' and ') || str_contains($name, ' & ')) {
            return $this->parseCompoundName($name);
        }

        // Single person parsing
        return $this->parseSinglePerson($name);
    }

    /**
     * Validate that a homeowner record contains required fields.
     *
     * Ensures the name is not empty, contains a valid title, and has
     * sufficient components for parsing.
     *
     * @param string $name The name to validate
     * @throws InvalidArgumentException When validation fails
     */
    private function validateRecord(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Name cannot be empty");
        }

        if (!preg_match('/(' . self::VALID_TITLES . ')/i', $name)) {
            throw new InvalidArgumentException("Name must contain a valid title");
        }

        $parts = preg_split('/\s+/', trim($name));

        if (count($parts) < 2) {
            throw new InvalidArgumentException("Name must contain at least two words");
        }
    }

    /**
     * Parse compound names containing multiple people.
     *
     * Handles names like "Mr and Mrs Smith" or "Dr & Mrs Joe Bloggs",
     * extracting individual person records for each title found.
     *
     * @param string $name The compound name to parse
     * @return array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}> Array of person records
     */
    private function parseCompoundName(string $name): array
    {
        $people = [];

        // Extract all titles
        preg_match_all('/(' . self::VALID_TITLES . ')\.?\s+/', $name, $matches);
        $titles = $matches[1] ?? [];

        // Remove titles first, then remove "and" or "&"
        $cleanName = preg_replace('/(' . self::VALID_TITLES . ')\.?\s+/i', '', $name);
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

    /**
     * Parse a single person name.
     *
     * Handles individual names like "Mr John Smith", extracting the title,
     * first name, and last name components.
     *
     * @param string $name The single person name to parse
     * @return array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}> Array containing single person record
     */
    private function parseSinglePerson(string $name): array
    {
        // Extract title
        $title = "";
        if (preg_match('/(' . self::VALID_TITLES . ')\.?\s+/', $name, $matches)) {
            $title = $matches[1];
        }

        // Extract names without titles
        $cleanName = preg_replace('/(' . self::VALID_TITLES . ')\.?\s+/i', '', $name);
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
