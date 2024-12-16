<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use \DateTime;
use Illuminate\Support\Facades\Log;
use App\Import\ChunkImport;
use App\Imports\ChunkImport as ImportsChunkImport;

class UploadFileController extends Controller
{
    private function escapeCsvRow($row)
    {
        // Escape each field in the row
        return implode(',', array_map(function ($field) {
            // If the field contains a comma, double quotes, or a newline, enclose it in double quotes
            if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                // Escape double quotes by doubling them (as per CSV format)
                $field = str_replace('"', '""', $field);
                return '"' . $field . '"';
            }
            return $field;
        }, $row));
    }


    // private function addPrefixToColumns($row, $header, $prefixMap)
    // {
    //     foreach ($row as $key => $value) {
    //         if (isset($header[$key])) {
    //             $columnName = $header[$key];

    //             if (isset($prefixMap[$columnName]) && !empty($value)) {
    //                 $row[$key] = $prefixMap[$columnName] . $value;
    //             }
    //         }
    //     }

    //     return $row;
    // }

    private function addPrefixToColumns($row, $header, $prefixMap)
{
    foreach ($row as $key => $value) {
        if (isset($header[$key])) {
            $columnName = $header[$key];

            if (isset($prefixMap[$columnName]) && !empty($value)) {
                // Get the prefix to check
                $prefix = $prefixMap[$columnName];

                // Check if the value already starts with the prefix
                if (strpos($value, $prefix) === 0) {
                    // Value already starts with the prefix, keep it unchanged
                    continue; // Skip to the next iteration
                } else {
                    // Value does not start with the prefix, add it
                    $row[$key] = $prefix . $value; // Add the prefix
                }
            }
        }
    }

    return $row;
}


public function exportToCSV(Request $request)
    {
        // Define the expected column names
        $expectedColumns = [
            'Sr no',
            'pan',
            'full_name',
            'epic_number',
            'driving_lic_number',
            'date_of_birth',
            'aadhar_number',
            'udyam_aadhar',
            'ckyc_number'
        ];

        // Store the uploaded file
        $path = $request->file('upload')->store('sv_files');
        $fileName = $request->file('upload')->getClientOriginalName();
        $file = Storage::path($path);

        // Load the Excel data
        $chunkImport = new ImportsChunkImport();
        Excel::import($chunkImport, $file);

        // Retrieve the header and rows after import
        $fileHeader = $chunkImport->getHeader();
        $rows = $chunkImport->getRowsData();
        $csvData = '';
        $overallErrorExists = false; // Flag to track if any errors exist
        // Check column count
        $columnCountCheck = $this->checkColumnCount($expectedColumns, $fileHeader);
        if ($columnCountCheck !== true) {
            return back()->with('error1', $columnCountCheck);
        }

        // Call the method to check column matching
        $columnCheckResult = $this->checkColumns($expectedColumns, $fileHeader);
        if ($columnCheckResult !== true) {
            return back()->with('error1', $columnCheckResult);
        }

        // Initialize CSV data with the header only once
      //  $csvData = $this->escapeCsvRow($fileHeader) . "\n";

        foreach ($rows as $rowIndex => $row) {
            if (is_array($row) && empty(array_filter($row))) {
                continue; // Skip empty rows
            }
            $errorMessage = ''; // Initialize error message for the current row
            $errorCount = 0; // Initialize error count for the current row

            // Define columns for validation
            $dateColumns = ['date_of_birth'];
            $panColumn = 'pan';
            $ckycColumn = 'ckyc_number';
            $requiredColumns = [
                'pan','full_name','epic_number','driving_lic_number','date_of_birth','aadhar_number','udyam_aadhar','ckyc_number'
            ];
            $aadharColumn = 'aadhar_number';
            // Process validation for each column in the row


            foreach ($fileHeader as $index => $columnName) {
                $value = $row[$index]; // Get current value

                // Validate Date columns
                if (in_array($columnName, $dateColumns)) {
                    $formattedDate = $this->validateDate($value, 'd-m-Y');
                    if ($formattedDate === false) {
                        $errorMessage .= "Invalid date format in '$columnName', ";
                    } else {
                        $row[$index] = $formattedDate; // Update row with formatted date
                    }
                }

                // Validate PAN
                if ($columnName === $panColumn) {
                    if (!$this->validatePAN($value)) {
                        $errorMessage .= "Invalid PAN format in '$columnName' column, ";
                    }
                }

                // Validate CKYC
                if ($columnName === $ckycColumn) {
                    if (!$this->validateCkycNumber($value)) {
                        $errorMessage .= "CKYC number must be exactly 14 characters in '$columnName' column, ";
                    }
                }

                // Validate required fields
                if (in_array($columnName, $requiredColumns)) {
                    if (!$this->validateRequiredField($value)) {
                        $errorMessage .= "Required field in '$columnName' column is empty, ";
                    }
                }

            }

            // If there were errors in the current row, append error message
            if (!empty($errorMessage)) {
                $overallErrorExists = true; // Set error flag to true
                // Remove trailing semicolon and space
                // $errorMessage = rtrim($errorMessage); // Trim trailing spaces
                if ($errorCount > 1) {
                    $row[] = rtrim($errorMessage, ', ') . ','; // Change last semicolon to full stop for multiple errors
                } else {
                    $row[] = rtrim($errorMessage, ', ') . '.'; // Keep it as full stop for single error
                }


              //  $row[] = rtrim($errorMessage, '; ') . '.'; // Append error message to the row
            }

            // Add prefixes for the columns in one pass
            $row = $this->addPrefixToColumns($row, $fileHeader, [
                'mobile_no' => 'mo',
                'ckyc_number' => 'ckyc',
                'aadhar_number' => 'aadhar'
            ]);

            // Add the row to CSV data
            $csvData .= $this->escapeCsvRow($row) . "\n"; // Add processed row to CSV data
        }

        // Build CSV data with error messages or without
        if ($overallErrorExists) {
            $header = $fileHeader;
            $header[] = "Error Message"; // Add error message column header
            $csvData = $this->escapeCsvRow($header) . "\n" . $csvData; // Build CSV with error column
        } else {
            $csvData = $this->escapeCsvRow($fileHeader) . "\n" . $csvData; // Build CSV without error column
        }

        // Store the CSV file
        $csvFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.csv';
        Storage::disk('local')->put($csvFileName, $csvData);

        if ($overallErrorExists) {
            session()->put('csvFileName', $csvFileName);
            return back()->with('error', 'Issues were detected in the uploaded file.<br><strong>Please Download Error File to check for details.</strong>')->with('showDownloadButton', true);
        } else {
            return response()->download(storage_path('app/' . $csvFileName))->deleteFileAfterSend(true);
        }

    }

    private function validateDate($date, $format = 'd-m-Y')
    {
        // Check if the date is numeric (Excel date serial number)
        if (is_numeric($date)) {
            try {
                // Convert Excel serial number to a DateTime object
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                // Log::info("Converted to DateTime object: " . $date->format('Y-m-d'));
            } catch (\Exception $e) {
                Log::error('Error converting Excel date: ' . $e->getMessage());
                return false;  // Return false for invalid date
            }
        }

        // Convert to string if it's a DateTime object
        if ($date instanceof \DateTime) {
            $date = $date->format($format);
        }

        // Log the processed date for debugging
        Log::info("Validating date: $date");

        // Validate the formatted date
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date ? $date : false;  // Return false if invalid
    }


    public function downloadErrorFile()
    {
        // Retrieve the error file name from the session
        $csvFileName = session('csvFileName');

        if ($csvFileName) {
            // Check if the file exists in the storage and proceed with the download
            return response()->download(storage_path('app/' . $csvFileName))->deleteFileAfterSend(true);
        }

        // If the file does not exist, redirect back with an error message
        return redirect()->back()->with('error', 'File not found.');
    }


    // Function to validate the Udyam number
    private function validateUdyamNumber($udyamNumber)
    {
        // Regular expression pattern for Udyam number
        $udyamPattern = "/^UDYAM-[A-Z]{2}-\d{2}-\d{7}$/";

        // Validate the Udyam number against the pattern
        return preg_match($udyamPattern, $udyamNumber) === 1 ? true : false;
    }

    // public function formatDate($date){
    //     $day =  substr($date,0,strlen($date) - 6);
    // 	$month = substr($date,strlen($date) -6 ,2);
    // 	$year = substr($date,-4);
    // 	$newFormat = $year.'-'.$month.'-'.str_pad($day,2,0,STR_PAD_LEFT);
    // 	return $newFormat;
    //     }

    private function checkColumnCount(array $expectedColumns, array $fileHeader)
    {
        if (count($expectedColumns) !== count($fileHeader)) {
            return '<strong>Column count mismatch.</strong><br>Expected ' . '<strong>'.count($expectedColumns) .'</strong>'. ' columns but found ' . '<strong>'.count($fileHeader) .'</strong>'.' columns. <br><br>Please Reffer Sample File Format for your reference';
        }
        return true; // Column count matches
    }


    private function checkColumns(array $expectedColumns, array $fileHeader)
    {
        // Compare the uploaded file's columns with the expected columns
        $missingColumns = array_diff($expectedColumns, $fileHeader);
        $extraColumns = array_diff($fileHeader, $expectedColumns);



        // Initialize the output message
            $messages = '<table class="table">';
            $messages .= '<tr><th>Required Columns</th><th>Wrong Columns Found</th></tr>'; // Table header

            // Use placeholders for missing and extra columns
            $missingColumnsList = !empty($missingColumns) ? implode('<br>', $missingColumns) : 'No Missing Columns';
            $extraColumnsList = !empty($extraColumns) ? implode('<br>', $extraColumns) : 'No Wrong Columns';

            // Add the missing and extra columns to the table
            $messages .= '<tr>';
            $messages .= '<td>' . $missingColumnsList . '</td>';
            $messages .= '<td>' . $extraColumnsList . '</td>';
            $messages .= '</tr>';
            $messages .= '</table>'; // Close the table

            return empty($missingColumns) ?true:$messages; // Return the formatted messages


    //         $messages = []; // Initialize an array to hold messages

    //     // Check if there are missing columns
    //     if (!empty($missingColumns)) {
    //         $messages[] = '<strong>Missing columns:</strong> '.'<br>' . implode('<br>', $missingColumns).'<br>';
    //     }

    //     // Check if there are extra columns
    //     if (!empty($extraColumns)) {
    //         $messages[] = '<br>'.'<strong>Wrong columns Found: </strong>'.'<br>' . implode('<br>', $extraColumns);
    //     }

    //     // If there are any messages, return them as a single string
    //     if (!empty($messages)) {
    //         return implode("\n", $messages); // Join the messages with new lines
    //     }

    //     return true; // Columns match

     }

    private function validatePAN($pan)
    {
        // Define the PAN regex pattern: 5 uppercase letters, 4 digits, 1 uppercase letter
        $panPattern = "/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/";

        // Validate the PAN using the regex pattern
        return preg_match($panPattern, $pan) === 1 ? true : false;  // Return error message if invalid
    }

    private function validateCkycNumber($ckycNumber)
    {
        $ckycNumber = trim($ckycNumber); // Remove any leading/trailing spaces
        return (strlen($ckycNumber) === 14 || strlen($ckycNumber) === 18) ? true : false;

        //return strlen(trim($ckycNumber)) === 14 ? true : false;  // Return error message if invalid
    }

    private function validateAmount($value)
    {
        // Check if the value is numeric and not empty
        return !empty($value) && is_numeric($value) ? true : false;  // Return error message if invalid
    }

    private function validateRequiredField($value)
    {
        // Check if the value is not empty (trimmed to avoid spaces being treated as valid)
        return !empty(trim($value)) ? true : false;  // Return error message if invalid
    }


    public function downloadSampleCSV()
    {
        // Define the expected column names
        $expectedColumns = [
            'Sr no',
            'pan',
            'full_name',
            'epic_number',
            'driving_lic_number',
            'date_of_birth',
            'aadhar_number',
            'ckyc_number'
        ];

        // Open a memory stream to hold the CSV data
        $handle = fopen('php://memory', 'w');

        // Add the header row
        fputcsv($handle, $expectedColumns);

        // Add a sample record (you can adjust these values as needed)
        $sampleRecord = [
            1,
            'JDXPS7881C',
            'First_name Middle_name Last_name',
            'ITR0046391',
            'HR7220180000466',
            'Enter date of birth in DD-MM-YYYY format e.g 13-08-1994',
            '265385644663'
        ];

        // Add the sample record to the CSV
        fputcsv($handle, $sampleRecord);

        // Move the file pointer to the beginning
        fseek($handle, 0);

        // Set headers to trigger a download response
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample.csv"',
        ];

        // Return the CSV content as a download
        return response()->stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, $headers);
    }


    public function createcsv()
    {
        return view("upload_file.upload");
    }
}




