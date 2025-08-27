<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Interface for parsing homeowner names from various sources.
 *
 * Defines the contract for services that can parse homeowner names
 * from strings or CSV files into structured data.
 */
interface NameParserInterface
{
    /**
     * Parse a homeowner name string into structured data.
     *
     * @param string $name The homeowner name to parse
     * @return array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}> Array of parsed person records
     */
    public function parseHomeowner(string $name): array;

    /**
     * Parse homeowner names from a CSV file upload.
     *
     * @param UploadedFile $file The uploaded CSV file
     * @return array{success: bool, count?: int, homeowners?: array<int, array{title: string, first_name: string|null, initial: string|null, last_name: string}>, error?: string} Result array
     */
    public function parseCsvFile(UploadedFile $file): array;
}