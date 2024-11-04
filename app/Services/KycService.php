<?php

namespace App\Services;

use App\Models\ProcessMeta;
use App\Models\GoldRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class KycService
{
    /***
     *
     */
    protected $baseUrl;
    protected $ClientId;
    protected $ClientSecret;
    protected $ProcessMetaService;

    public function __construct(ProcessMetaService $processMetaService)
    {
        $this->baseUrl = config('services.kyc_api.base_url'); // Base URL from config
        $this->ClientId = config('services.kyc_api.ClientId'); // Fetch from config or env
        $this->ClientSecret = config('services.kyc_api.ClientSecret'); // Fetch from config or env
        $this->ProcessMetaService = $processMetaService;
    }
    protected function post($endpoint, $data = [])
    {
	    /**
         *
         */
        // Initialize cURL
	    // Log the request
	//dd($this->ClientSecret,$this->ClientId,$this->baseUrl . $endpoint);  
	$headers = [
            'ClientSecret' => $this->ClientSecret,
            'ClientId' => $this->ClientId,
            'Content-Type' => 'application/json',
    ];
        Log::info('POST Request', [
            'url' => $this->baseUrl . $endpoint,
            'headers' => $headers,
            'data' => $data,
    ]);
	//dd($this->baseUrl . $endpoint);
        $ch = curl_init($this->baseUrl . $endpoint);
        // Set the options for the cURL request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting
        curl_setopt($ch, CURLOPT_POST, true); // This is a POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Set the POST data
        curl_setopt($ch, CURLOPT_PROXY, "10.128.106.4:8080");
	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	curl_setopt($ch,CURLOPT_VERBOSE,true);
        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'ClientId: ' . $this->ClientId,
            'ClientSecret: ' . $this->ClientSecret,
            'Content-Type: application/json'
        ]);
	//dd($ch);
        // Execute the request and get the response
        $response = curl_exec($ch);
        //dd($response);
	
	// Check for any errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
	    dd($error_msg);
        }
        return json_decode($response, TRUE);
    }
    public function PanVerification($pan, $data)
    {
        /**
         * PAN Verification using scoreme API
         */
        $arr = array(
            'pan' => $pan
        );
        $request = array(
            'endpoint' => $this->baseUrl . '/panDataFetch',
            'data' => $arr
        );
        $request_json = json_encode($request);
        try {
	    $res = $this->post('/panDataFetch', json_encode($arr));
            dd($res);
	    if ($res['responseCode'] == 'SRC001') {
                $pan_details = $res['data'];
                //dd($pan_details);
                if (($pan == $pan_details['pan']) && ($pan_details['panStatus'] == 'PAN is Active and the details are matching with PAN database.')) {
                    //return $pan_details;
                    $api_status = 'S';
                    $process_status = 'completed';
                } else {
                    $api_status = 'F';
                    $process_status = 'failed';
                }
            } else {
                $api_status = 'F';
                $process_status = 'failed';
            }
        } catch (\Exception $e) {
            $res = response()->json(['error' => $e->getMessage()], 500);
            $api_status = 'F';
            $process_status = 'failed';
        }
        //$this->ProcessMetaService->add($data, $request_json, json_encode($res), $api_status, $process_status,'PanVerification');
        if ($api_status == 'S') {
            return $pan_details;
        } else {
            return false;
        }
    }
    public function udyamVerification($udyam_aadhar, $data)
    {
        /**
         * PAN Verification using scoreme API
         */
        $arr = array(
            'registrationnumber' => $udyam_aadhar
        );
        $endpoint = '/udyamRegistration';
        $request = array(
            'endpoint' => $this->baseUrl . $endpoint,
            'data' => $arr
        );
       // dd($udyam_aadhar );
        $request_json = json_encode($request);
	try {
            $response = $this->post('/udyamRegistration', json_encode($arr));
	  // dd($response['responseCode']);
	    if ($response['responseCode'] == 'SRC001') {
                $udyam_details = $response['data'];
                if ($udyam_aadhar == $udyam_details['udyamRegistrationNumber']) {
                    //return $pan_details;
                    $api_status = 'S';
                    $process_status = 'completed';
                } else {
                    $api_status = 'F';
                    $process_status = 'failed';
                }
            } else {
                $api_status = 'F';
                $process_status = 'failed';
            }
        } catch (\Exception $e) {
            $response = response()->json(['error' => $e->getMessage()], 500);
            $api_status = 'F';
            $process_status = 'failed';
	}

     $this->ProcessMetaService->add($data, $request_json, json_encode($response), $api_status, $process_status,'udyamVerification');
        if ($api_status == 'S') {
            return $udyam_details;
        } else {
            return false;
        }

    }
    protected function post1($endpoint, $data = [])
    {
        //dd($data);
        $headers = [
            'ClientSecret' => $this->ClientSecret,
            'ClientId' => $this->ClientId,
            'Content-Type' => 'application/json',
        ];

        // Log the request
        Log::info('POST Request', [
            'url' => $this->baseUrl . $endpoint,
            'headers' => $headers,
            'data' => $data,
        ]);
        /*
        $response = Http::withOptions([
            'proxy' => env('PROXY', '10.128.106.4:8080'),
        ])->withHeaders($headers)->post($this->baseUrl . $endpoint, $data);
        */
        $response = Http::withHeaders($headers)->post($this->baseUrl . $endpoint, $data);

        //dd($response->body());
        Log::info('POST Response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ]);
        return $response;
    }

public function goldRateUpdate()
    {
        /**
         * Gold Rate Update using the bullionRates API
         */
        $data = [
            'commodityType' => 'GOLD',
            'applicationId' => ''
        ];
        $endpoint = '/bullionRates';
        $request_json = json_encode(['endpoint' => $this->baseUrl . $endpoint,'data' => $data]);
        //dd($request_json);
        try {
            $response = $this->post2($endpoint, $data);
            dd($response);
            $response_json = json_encode($response);
            if (isset($response['data'])) {
                $goldRate = $response['data']['22KGoldPricePerGram'];
                $gold_24k_rate = $response['data']['24KGoldPricePerGram'];
                $update_rate_at = $response['data']['updatedDateAndTime'];
                $entryDate = Carbon::now()->format('Y-m-d');

                Log::info('Storing gold rate:', [
                    'entry_date' => $entryDate,
                    '22k_gold_rate' => $goldRate,
                    '24k_gold_rate' => $gold_24k_rate,
                    'updated_time' => $update_rate_at
                ]);

                try {
                    // Storing the gold rate in the database
                    GoldRate::create([
                        '22k_gold_rate' => $goldRate,
                        '24k_gold_rate' => $gold_24k_rate,
                        'updated_time' => $update_rate_at,
                        'entry_date' => $entryDate,
                        'request' => $request_json,
                        'response' => $response_json,
                    ]);

				 $api_status = 'S';
                    $process_status = 'completed';
                    Log::info('Gold rate updated successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to store gold rate: ' . $e->getMessage());
                    $api_status = 'F';
                    $process_status = 'failed';
                }
            } else {
                Log::error('Failed to update gold rate: Data key not found in API response.');
                $api_status = 'F';
                $process_status = 'failed';
            }
        } catch (\Exception $e) {
            Log::error('Failed to update gold rate: ' . $e->getMessage());
            $api_status = 'F';
            $process_status = 'failed';
        }

        // Store request and response meta
        //$this->ProcessMetaService->add($data, $request_json, json_encode($response), $api_status, $process_status, 'goldRateUpdate');

       /* if ($api_status == 'S') {
            return true;
        } else {
            return false;
        } */

        return $api_status == 'S';
    }
	 protected function post2($endpoint, $data = [])
    {
          $headers = [
            'ClientSecret' => $this->ClientSecret,
            'ClientId' => $this->ClientId,
            'Content-Type' => 'application/json',
    ];
        Log::info('POST Request', [
            'url' => $this->baseUrl . $endpoint,
            'headers' => $headers,
            'data' => $data,
    ]);
        //dd($data);
        $ch = curl_init($this->baseUrl . $endpoint);
        // Set the options for the cURL request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting
        curl_setopt($ch, CURLOPT_POST, true); // This is a POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data)); // Set the POST data
        curl_setopt($ch, CURLOPT_PROXY, env('PROXY', '10.128.106.4:8080'));
        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'ClientId: ' . $this->ClientId,
            'ClientSecret: ' . $this->ClientSecret,
            'Content-Type: application/json'
        ]);
        // Execute the request and get the response
        $response = curl_exec($ch);
        dd($response);

        // Check for any errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        return json_decode($response, TRUE);

    }

 



    public function PanVerification1($pan, $data)
    {
        /**
         * PAN Verification using scoreme API
         */
        $arr = array(
            'pan' => $pan
        );
        $request = array(
            'endpoint' => $this->baseUrl . '/panDataFetch',
            'data' => $arr
        );
        $request_json = json_encode($request);
        try {
            $response = $this->post('/panDataFetch', $arr);
            $res = response()->json($response);
            if ($response->status() == 200) {
                $body = json_decode($response->body(), True);
                $pan_details = $body['data'];
                if (($pan == $pan_details['pan']) && ($pan_details['panStatus'] == 'PAN is Active and the details are matching with PAN database.')) {
                    //return $pan_details;
                    $api_status = 'S';
                    $process_status = 'completed';
                } else {
                    $api_status = 'F';
                    $process_status = 'failed';
                }
            } else {
                $api_status = 'F';
                $process_status = 'failed';
            }
        } catch (\Exception $e) {
            $res = response()->json(['error' => $e->getMessage()], 500);
            $api_status = 'F';
            $process_status = 'failed';
        }
        $this->ProcessMetaService->add($data, $request_json, $response->body(), $api_status, $process_status);
        if ($api_status == 'S') {
            return $pan_details;
        } else {
            return false;
        }
    }
    public function udyamVerification1($udyam_aadhar, $data)
    {
        /**
         * PAN Verification using scoreme API
         */
        $arr = array(
            'registrationnumber' => $udyam_aadhar
        );
        $endpoint = '/udyamRegistration';
        $request = array(
            'endpoint' => $this->baseUrl . $endpoint,
            'data' => $arr
        );
        $request_json = json_encode($request);

        try {
            $response = $this->post($endpoint, $arr);

            if ($response->status() == 200) {
                $body = json_decode($response->body(), True);
                $pan_details = $body['data'];
                //dd($pan_details);
                if ($udyam_aadhar == $pan_details['udyamRegistrationNumber']) {
                    $api_status = 'S';
                    $process_status = 'completed';
                } else {
                    $api_status = 'F';
                    $process_status = 'failed';
                }
            } else {
                $api_status = 'F';
                $process_status = 'failed';
            }
        } catch (\Exception $e) {
            $res = response()->json(['error' => $e->getMessage()], 500);
            $api_status = 'F';
            $process_status = 'failed';
        }
        $this->ProcessMetaService->add($data, $request_json, $response->body(), $api_status, $process_status);
        if ($api_status == 'S') {
            return $pan_details;
        } else {
            return false;
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
