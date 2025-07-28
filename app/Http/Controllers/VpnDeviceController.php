<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\CreateVpnDeviceJob;
use App\Jobs\DeleteVpnDeviceJob;

class VpnDeviceController extends Controller
{
    public function create(Request $request)
{
    $validated = $request->validate([
        'device_name'   => 'required|string|alpha_dash',
        'device_ip'     => 'required|ip',
        'device_subnet' => 'required|string',
    ]);

    CreateVpnDeviceJob::dispatch(
        $validated['device_name'],
        $validated['device_ip'],
        $validated['device_subnet']
    );

    return back()->with('success', "🕒 Creating VPN device '{$validated['device_name']}' in background...");
}

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'device_name'   => 'required|string|alpha_dash',
            'device_ip'     => 'required|ip',
            'device_subnet' => 'required|string',
        ]);

        DeleteVpnDeviceJob::dispatch(
            $validated['device_name'],
            $validated['device_ip'],
            $validated['device_subnet']
        );

        return response()->json(['message' => 'Delete job dispatched successfully.']);
    }
}
