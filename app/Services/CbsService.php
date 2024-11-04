<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CbsService
{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.cbs_api.base_url'); // Base URL from config

    }
    protected function post($endpoint, $data = [])
    {
        // Log request details
        Log::info('Sending POST request to:', [
            'url' => $this->baseUrl . $endpoint,
            'data' => $data
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . $endpoint, $data);
        Log::info('Received response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        return $this->handleResponse($response);
    }
    public function PanEnquiry($pan_number)
    {
        /**
         *
         */
        $requestId = 'req' . time();
        $arr = array(
            "data" => array(
                'requestId' => $requestId,
                'requestType' => 'PanEnquiry',
                'panNo' => $pan_number
            ),
            "reqTimestamp" => time(),
            "vendor" => "COLENDING",
            "requestId" => $requestId,
            "client" => "BOM",
            "chksum" => "13456"
        );
        try {
            $response = $this->post('/OTAPI/COLENDING/PanEnquiry', $arr);
            $res = response()->json($response);
            dd($res);
        } catch (\Exception $e) {
            $res = response()->json(['error' => $e->getMessage()], 500);
            dd($res);
        }
    }
    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('API request failed with status ' . $response->status());
    }
}
