<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\OpenVpnClientCreationJob;

class OpenVpnClientController extends Controller
{
    public function createClient(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|alpha_dash',
        ]);

        $username = $validated['client_name'];

        OpenVpnClientCreationJob::dispatch($username);

        return back()
            ->with('success', "🕒 VPN client '{$username}' is being created in the background...")
            ->with('vpn_username', $username);
    }
}
