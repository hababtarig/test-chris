<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScriptRunnerController extends Controller
{
    public function createLinuxUser(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'username'   => 'required|string|alpha_dash',
            'password'   => 'required|string|confirmed',
            'public_key' => 'required|string',
        ]);

        $username = $validated['username'];
        $password = $validated['password'];
        $publicKey = $validated['public_key'];

        // Get OpenVPN server IP from .env
        $serverIp = env('OPENVPN_IP');
        if (!$serverIp) {
            return back()->with('error', 'OpenVPN server IP not configured.');
        }

        // Get SSH config from .env
        $pemPath = env('EC2_KEY_PATH');
        $sshUser = env('EC2_USER', 'ec2-user');

        // Escape arguments
        $escapedUsername = escapeshellarg($username);
        $escapedPassword = escapeshellarg($password);
        $escapedPublicKey = escapeshellarg($publicKey);

        // Build SSH command
        $sshCommand = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s bash ~/create_linux_user.sh %s %s %s',
            escapeshellarg($pemPath),
            escapeshellarg($sshUser),
            escapeshellarg($serverIp),
            $escapedUsername,
            $escapedPassword,
            $escapedPublicKey
        );

        exec($sshCommand, $output, $exitCode);

        if ($exitCode === 0) {
            return back()->with('success', "✅ User '$username' created successfully on OpenVPN server.");
        } else {
            return back()->with('error', "❌ Failed to create user. Output:\n" . implode("\n", $output));
        }
    }
}
