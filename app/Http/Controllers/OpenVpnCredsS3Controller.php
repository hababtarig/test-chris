<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\OpenVpnCredsAddS3Job;
use App\Jobs\OpenVpnCredsDeleteS3Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class OpenVpnCredsS3Controller extends Controller
{

public function add(Request $request)
{
    try {
        $validated = $request->validate([
            'cert_name' => 'required|string|alpha_dash',
            'key_name'  => 'required|string|alpha_dash',
            'ovpn_name' => 'required|string|alpha_dash',
        ]);

        OpenVpnCredsAddS3Job::dispatch(
            $validated['cert_name'],
            $validated['key_name'],
            $validated['ovpn_name']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "🕒 Uploading VPN credentials to S3 for '{$validated['cert_name']}'..."
            ]);
        }

        return back()->with('success', "🕒 Uploading VPN credentials to S3 for '{$validated['cert_name']}'...");
    } catch (ValidationException $e) {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => '⚠️ Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        throw $e;
    }
}

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'cert_name' => 'required|string|alpha_dash',
            'key_name'  => 'required|string|alpha_dash',
            'ovpn_name' => 'required|string|alpha_dash',
        ]);

        OpenVpnCredsDeleteS3Job::dispatch(
            $validated['cert_name'],
            $validated['key_name'],
            $validated['ovpn_name']
        );

        return response()->json(['message' => 'VPN credentials delete job dispatched.']);
    }
}
