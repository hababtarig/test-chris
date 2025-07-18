<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\DeleteLinuxUserJob;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'server'   => 'required|in:openvpn,ftp,haproxy',
            'username' => 'required|string|alpha_dash',
        ]);

        $serverKey = $validated['server'];
        $username  = $validated['username'];

        // Confirm config exists
        $controllerIp = config("services.servers.$serverKey.controller_ip");
        $targetIp     = config("services.servers.$serverKey.ip");

        if (!$controllerIp || !$targetIp) {
            Log::error('❌ Server configuration is incomplete.', [
                'server'        => $serverKey,
                'controller_ip' => $controllerIp,
                'target_ip'     => $targetIp,
            ]);
            return back()->withErrors(['server' => 'Server configuration is missing or incomplete.']);
        }

        // Dispatch job with server key only (not raw IPs)
        DeleteLinuxUserJob::dispatch($serverKey, $username);

        return back()->with('status', "✅ Deleting Linux user '{$username}' on '{$serverKey}' server...");
    }
}
