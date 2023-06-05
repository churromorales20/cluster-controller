<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\DockerHelper;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;

class TenantBotController extends Controller
{
    const IMAGE_NAME = 'uchip-whatsapp-bot-image';

    public function checkIfRunning(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|integer|exists:cluster.tenants,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $containerName = 'WhatsappBot_' . $request->input('tenant_id');
        return response()->json([
            'status' => 'success',
            'bot_status' => DockerHelper::checkContainerStatus($containerName)
        ]);
    }

    public function stopWhatsapp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|integer|exists:cluster.tenants,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $containerName = 'WhatsappBot_' . $request->input('tenant_id');
        DockerHelper::stopContainer($containerName);
        return response()->json([
            'status' => 'success',
        ]);
    }

    public function startWhatsapp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|integer|exists:cluster.tenants,id',
            'api_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $tenant = Tenant::where('id', $request->input('tenant_id'))->first();
        $containerName = 'WhatsappBot_' . $tenant->id;
        $containerStatus = DockerHelper::checkContainerStatus($containerName);
        if($containerStatus['running'] === false){
            $data_container = [
                'presence' => $containerStatus['presence'],
                'name' => $containerName,
                'environments' => [
                    'TENANT_API' => $request->input('api_url')
                ],
                'image' => 'uchip-whatsapp-bot-image:latest'
            ];
            
            $command = DockerHelper::runContainer($data_container);
            return response()->json([
                'status' => 'success',
                'command' => $command,
                'ip' => DockerHelper::getContainerIP($containerName)
            ]);
        }
        return response()->json([
            'status' => 'error',
        ]);

    }
}