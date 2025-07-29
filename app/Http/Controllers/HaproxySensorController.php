<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AddHaproxySensorJob;
use App\Jobs\DeleteHaproxySensorJob;

class HaproxySensorController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'sensor_name'    => 'required|string|alpha_dash',
            'frontend_port'  => 'required|numeric',
            'backend_ip'     => 'required|ip',
            'backend_port'   => 'required|numeric',
        ]);

        AddHaproxySensorJob::dispatch(
            $validated['sensor_name'],
            $validated['frontend_port'],
            $validated['backend_ip'],
            $validated['backend_port']
        );

        return back()->with('success', "🕒 Creating HAProxy sensor '{$validated['sensor_name']}' in background...");
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'sensor_name'    => 'required|string|alpha_dash',
            'frontend_port'  => 'required|numeric',
            'backend_ip'     => 'required|ip',
        ]);

        DeleteHaproxySensorJob::dispatch(
            $validated['sensor_name'],
            $validated['frontend_port'],
            $validated['backend_ip']
        );

        return response()->json(['message' => 'Delete job dispatched successfully.']);
    }
}
