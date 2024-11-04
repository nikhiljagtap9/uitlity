<?php

namespace App\Services;

use App\Models\ProcessMeta;
use Illuminate\Support\Facades\Auth;

class ProcessMetaService
{
    public function add($data, $request_json, $response_json, $api_status, $process_status,$meta_type=NULL)
    {
        $process_id = ProcessMeta::generateUuid();
        $data->meta()->createMany([
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'process_id', 'meta_value' => $process_id],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'vendor', 'meta_value' => 'Scoreme'],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'request_json', 'meta_value' => $request_json],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'response_json', 'meta_value' => $response_json],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'api_status', 'meta_value' => $api_status],
            ['process_type' => 'Disbursement', 'process_id' => $process_id, 'process_owner' => Auth::user()->name, 'meta_type' => $meta_type, 'meta_key' => 'process_status', 'meta_value' => $process_status],
        ]);
    }
}
