<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function create()
    {
        $setting = Setting::latest()->first();
        $settings = Setting::orderBy('id', 'desc')->get();
        return view('setting.create', ['setting' => $setting, 'settings' => $settings]);
    }
    public function store(Request $request)
    {
        $data = $request->all();
        Setting::create([
	    'bank_interest' => $data['bank_roi']-($data['service_fee']+($data['service_fee']*$data['gst']/100)),
	    'benchmark_rate' => $data['benchmark_rate'],
	    'bank_roi' => $data['bank_roi'],
	    'service_fee' => $data['service_fee'],
	    'gst' => $data['gst'],
            'nbfc_interest' => $data['nbfc_interest'],
            'benchmark_rate' => $data['benchmark_rate'],
            // 'loan_account_number' => $data['loan_account_number'],
            'loan_account_number_agri' => $data['loan_account_number_agri'],
	    'loan_account_number_msme' => $data['loan_account_number_msme'],
	    'to_loan_account_number_agri' => $data['to_loan_account_number_agri'],
	    'to_loan_account_number_msme' => $data['to_loan_account_number_msme'],

	    // Store true if checkbox is checked, otherwise store false
            'pan_check' => $request->has('pan_checkbox') ? "true" : "false",
            'ckyc_check' => $request->has('ckyc_checkbox') ? "true" : "false",
            'udyam_check' => $request->has('udyam_checkbox') ? "true" : "false",
        ]);
        return redirect()->back()->with('success', 'Settings has been saved.');
    }

    // Function to convert a pipe-separated text file to a CSV file
    private function convertPsvToCsv($inputFile, $outputFile)
    {
        // Open the input pipe-separated text file for reading
        $inputHandle = fopen($inputFile, 'r');
        if ($inputHandle === false) {
            die("Error: Unable to open input file $inputFile.\n");
        }

        // Open the output CSV file for writing
        $outputHandle = fopen($outputFile, 'w');
        if ($outputHandle === false) {
            fclose($inputHandle);
            die("Error: Unable to open output file $outputFile.\n");
        }
        $i = 0;
        // Read each line from the pipe-separated file
        while (($line = fgets($inputHandle)) !== false) {
            if ($i == 0) {
                $line = strtolower($line);
                $find =  array('(', ')', ',', '.', ' ');
                $replace = array('', '', '', '', '', '_');
                $line2 = str_replace($find, $replace, $line);
            } else {
                $find =  array('(', ')', ',', '"');
                $replace = array('', '', '', '');
                $line2 = str_replace($find, $replace, $line);
            }

            // Split the line by the pipe character to get an array of fields
            $fields = explode('|', trim($line2));

            // Write the array of fields to the CSV file
            fputcsv($outputHandle, $fields, ',');
            $i++;
        }

        // Close both input and output files
        fclose($inputHandle);
        fclose($outputHandle);
    }

    public function create_textcsv()
    {
        return view('textcsv');
    }
    public function textcsv(Request $request)
    {
        /** */
        $file = $request->file('upload');
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $i = 0;
            $outputHandle = fopen('php://output', 'w');
            // Read each line from the pipe-separated file
            while (($line = fgetcsv($handle, 1000, ',')) !== false) {
                if ($i == 0) {
                    $line = strtolower($line);
                    $find =  array('(', ')', ',', '.', ' ');
                    $replace = array('', '', '', '', '', '_');
                    $line2 = str_replace($find, $replace, $line);
                } else {
                    $find =  array('(', ')', ',', '"');
                    $replace = array('', '', '', '');
                    $line2 = str_replace($find, $replace, $line);
                }

                // Split the line by the pipe character to get an array of fields
                $fields = explode('|', trim($line2));

                // Write the array of fields to the CSV file
                fputcsv($outputHandle, $fields, ',');
                $i++;
            }
            // Close both input and output files
            fclose($handle);
            fclose($outputHandle);
            $filename = "csv_" . date('Y-m-d') . "-" . time() . ".csv";
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            return response()->stream($outputHandle, 200, $headers);
        }
    }
}
