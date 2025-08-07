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
    $validated = $request->validate([
        'cert_name' => 'nullable|string|alpha_dash',
        'key_name'  => 'nullable|string|alpha_dash',
        'ovpn_name' => 'nullable|string|alpha_dash',
    ]);

    if (empty($validated['cert_name']) && empty($validated['key_name']) && empty($validated['ovpn_name'])) {
        return response()->json([
            'message' => '⚠️ At least one filename must be provided.'
        ], 422);
    }

    OpenVpnCredsAddS3Job::dispatch(
        $validated['cert_name'] ?? '',
        $validated['key_name'] ?? '',
        $validated['ovpn_name'] ?? ''
    );

    if ($request->wantsJson()) {
        return response()->json([
            'message' => "🕒 Uploading VPN credentials to S3..."
        ]);
    }

    return back()->with('success', "🕒 Uploading VPN credentials to S3...");
}


    public function delete(Request $request)
{
    $validated = $request->validate([
        'cert_name' => 'nullable|string|alpha_dash',
        'key_name'  => 'nullable|string|alpha_dash',
        'ovpn_name' => 'nullable|string|alpha_dash',
    ]);

    if (empty($validated['cert_name']) && empty($validated['key_name']) && empty($validated['ovpn_name'])) {
        return response()->json([
            'message' => '⚠️ At least one filename must be provided.'
        ], 422);
    }

    OpenVpnCredsDeleteS3Job::dispatch(
        $validated['cert_name'] ?? '',
        $validated['key_name'] ?? '',
        $validated['ovpn_name'] ?? ''
    );

    return response()->json(['message' => '🕒 Deleting VPN credentials from S3...']);
}

}
