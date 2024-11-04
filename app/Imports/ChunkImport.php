<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;


class ChunkImport implements ToCollection, WithChunkReading
{
    protected static $headerProcessed = false;
    protected $header;
    protected $rowsData = []; // Store rows data here

    public function collection(Collection $rows)
    {
        // Handle the header only once in the first chunk
        if (!self::$headerProcessed) {
            $this->header = $rows->shift(); // Remove the header row
            self::$headerProcessed = true;

            // You can log or handle the header as needed
            Log::info('File Header:', $this->header->toArray());
        }

        // Store the current chunk of rows
        foreach ($rows as $row) {
            $this->rowsData[] = $row->toArray(); // Convert to array and store
        }
    }

    public function chunkSize(): int
    {
        return 600; // Adjust chunk size as needed
    }

    public function getHeader()
    {
        return $this->header ? $this->header->toArray() : null; // Return the header as an array
    }

    public function getRowsData()
    {
        return $this->rowsData; // Return the stored rows data
    }
}




