<?php

namespace App\Http\Controllers;
use App\Jobs\OpenVpnClientCreationJob;
use Illuminate\Http\Request;
use App\Models\VpnUser;


class OpenVpnClientController extends Controller
{
    

public function createVpnUser(Request $request)
{
    $username = $request->input('username');

    $vpnUser = VpnUser::create([
        'name' => $username,
        'status' => 'pending',
    ]);

    OpenVpnClientCreationJob::dispatch($username);

    return response()->json(['message' => "🕒 Creating VPN client '$username' in background..."]);
}

    public function createClient(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|alpha_dash',
        ]);

        // Dispatch the job to background queue
        OpenVpnClientCreationJob::dispatch($validated['client_name']);
return back()
    ->with('success', "🕒 Creating VPN client '{$validated['client_name']}' in background...")
    ->with('vpn_username', $validated['client_name']);


    }
}

