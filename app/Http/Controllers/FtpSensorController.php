<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AddFtpSensorJob;
use App\Jobs\DeleteFtpSensorJob;

class FtpSensorController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'sensor_name' => 'required|string|alpha_dash',
        ]);

        AddFtpSensorJob::dispatch(
            $validated['sensor_name']
        );

        return back()->with('success', "🕒 Creating FTP sensor '{$validated['sensor_name']}' in background...");
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'sensor_name' => 'required|string|alpha_dash',
        ]);

        DeleteFtpSensorJob::dispatch(
            $validated['sensor_name']
        );

        return response()->json(['message' => 'FTP delete job dispatched.']);
    }
}
