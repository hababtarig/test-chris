<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteLinuxUserJob;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Create task record
        $task = Task::create([
            'type'     => 'delete_linux_user',
            'server'   => $serverKey,
            'username' => $username,
            'status'   => 'pending',
        ]);

        // Dispatch job with task ID
        DeleteLinuxUserJob::dispatch($serverKey, $username, $task->id);

        return back()->with('success', "🕒 Deleting Linux user '{$username}' on '{$serverKey}' server in background...");
    }
}
