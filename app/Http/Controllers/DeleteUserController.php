<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Jobs\DeleteLinuxUserJob;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{public function delete(Request $request)
{
    $validated = $request->validate([
        'server'   => 'required|in:openvpn,ftp,haproxy',
        'username' => 'required|string|alpha_dash',
    ]);

    $serverKey = $validated['server'];
    $username = $validated['username'];

    $host = config("services.servers.$serverKey.ip");
    $privateKey = config('services.ec2.key_path');
    $remoteUser = config('services.ec2.user');

    if (!$host || !$privateKey || !$username) {
        Log::error('Missing Linux user deletion job parameters', [
            'host' => $host,
            'privateKey' => $privateKey,
            'username' => $username,
        ]);
        return back()->withErrors(['server' => 'Server configuration is incomplete.']);
    }



   DeleteLinuxUserJob::dispatch($host, $privateKey, $remoteUser, $username);


    return back()->with('status', "Deleting Linux user '{$username}' on '{$serverKey}' server...");
}

}
