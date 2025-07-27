<?php

namespace App\Http\Controllers;

use App\Jobs\CreateOvpnFileJob;
use App\Jobs\DeleteOvpnFileJob;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VpnFileController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'server'      => 'required|in:openvpn',  // adjust if you have other servers
            'client_name' => 'required|string|alpha_dash',
        ]);

        $serverKey = $validated['server'];
        $clientName = $validated['client_name'];

        $controllerIp = config("services.servers.$serverKey.controller_ip");
        if (!$controllerIp) {
            Log::error('❌ VPN create: Missing controller IP.', ['server' => $serverKey]);
            return back()->withErrors(['server' => 'Server configuration is missing or incomplete.']);
        }

        $task = Task::create([
            'type'     => 'create_openvpn_file',
            'server'   => $serverKey,
            'username' => $clientName,
            'status'   => 'pending',
        ]);

        CreateOvpnFileJob::dispatch($serverKey, $clientName, $task->id);

        return back()->with('success', "🕒 Creating OVPN file for '{$clientName}' on '{$serverKey}'...");
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'server'      => 'required|in:openvpn',  // adjust if you have others
            'client_name' => 'required|string|alpha_dash',
        ]);

        $serverKey = $validated['server'];
        $clientName = $validated['client_name'];

        $controllerIp = config("services.servers.$serverKey.controller_ip");
        if (!$controllerIp) {
            Log::error('❌ VPN delete: Missing controller IP.', ['server' => $serverKey]);
            return back()->withErrors(['server' => 'Server configuration is missing or incomplete.']);
        }

        $task = Task::create([
            'type'     => 'delete_openvpn_file',
            'server'   => $serverKey,
            'username' => $clientName,
            'status'   => 'pending',
        ]);

        DeleteOvpnFileJob::dispatch($serverKey, $clientName, $task->id);

        return back()->with('success', "🕒 Deleting OVPN file for '{$clientName}' on '{$serverKey}'...");
    }
}
