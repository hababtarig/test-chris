<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\LinuxUserCreationJob;


class ScriptRunnerController extends Controller
{
   
   public function createLinuxUser(Request $request)
    {
        $validated = $request->validate([
            'server'     => 'required|in:openvpn,ftp,haproxy',
            'username'   => 'required|string|alpha_dash',
            'password'   => 'required|string|confirmed',
            'public_key' => 'required|string',
        ]);

        LinuxUserCreationJob::dispatch(
            $validated['server'],
            $validated['username'],
            $validated['password'],
            $validated['public_key']
        );

        return back()->with('success', "🕒 Creating Linux user '{$validated['username']}' in background...");
    }
}
