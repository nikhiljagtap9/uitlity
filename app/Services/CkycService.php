<?php

namespace App\Services;

use App\Models\ProcessMeta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CkycService
{
    /***
     *
     */
    protected $baseUrl;
    protected $authToken;
    protected $clientAuth;

    public function __construct()
    {
        $this->baseUrl = config('services.ckyc_api.base_url'); // Base URL from config
        $this->authToken = config('services.ckyc_api.auth_token'); // Fetch from config or env
        $this->clientAuth = config('services.ckyc_api.client_auth'); // Fetch from config or env

    }
    
    public function post($endpoint, $data = [])
    {
       // dd($data);
        $multipartData = [];

        foreach ($data as $key => $value) {
            $multipartData[] = [
                'name' => $key,
                'contents' => $value
            ];
        }
        Log::info('Sending POST request to:', [
            'endpoint' => $this->baseUrl . $endpoint,
            'data' => $multipartData
        ]);
	//dd($this->baseUrl . $endpoint);
        $response = Http::withHeaders([
            'Authentication-token' => $this->authToken,
            'Client-Auth' => $this->clientAuth

        ])->asMultipart()->post($this->baseUrl . $endpoint, $multipartData);
        //dd($response);
	$xmlContent = $response->body();
        //dd($xmlContent);
	
	$xmlObject = simplexml_load_string($xmlContent);
        if ($xmlObject === false) {
            throw new \Exception('Failed to parse xml response');
        }

        //$xmlArray = json_decode(json_encode($xmlObject), True);
        //dd($xmlArray);
        return json_encode($xmlObject);
    }
    public function ckycverify($ckyc,$dob,$data)
    {
	   // dd("HELLO");
        /***
         *
         */
        $arr = [
            'CKYC_NO' => $ckyc,
            'AUTH_FACTOR_TYPE' => 01,
            'AUTH_FACTOR' => $dob,
            'DOB' => $dob
        ];
                $request = array(
                        'endpoint'=> $this->baseUrl.'/GetDownloadRequest',
                        'data'=>$arr
                );
        $request_json = json_encode($request);
    
	try {
            $response = $this->post('/GetDownloadRequest', $arr);
            //$res = response()->json($response);
       // dd('ckyc resd',$response);   
	 $res =  $response;
            $api_status = 'S';
            $process_status = 'completed';
        } catch (\Exception $e) {
            //dd($e->getMessage());
	    $res = response()->json(['error' => $e->getMessage()], 500);
            $api_status = 'F';
            $process_status = 'failed';
	}
        $process_id = ProcessMeta::generateUuid();
        $data->meta()->createMany([
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'process_id', 'meta_value' => $process_id],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'vendor', 'meta_value' => 'Adroit'],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'request_json', 'meta_value' => $request_json],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'response_json', 'meta_value' => $res],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'api_status', 'meta_value' => $api_status],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => 0, 'meta_type' => 'CkycVerification', 'meta_key' => 'process_status', 'meta_value' => $process_status],
    ]);
        if ($api_status == 'S') {
            return $res;
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
