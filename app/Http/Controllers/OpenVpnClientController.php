<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpenVpnClientController extends Controller
{
    public function createClient(Request $request)
{
    $validated = $request->validate([
        'client_name' => 'required|string|alpha_dash',
    ]);

    $clientName = $validated['client_name'];

    $serverIp = env('OPENVPN_IP');
    $pemPath = escapeshellarg(env('EC2_KEY_PATH'));
    $sshUser = escapeshellarg(env('EC2_USER', 'ec2-user'));

    if (empty($serverIp)) {
        return back()->with('error', 'OpenVPN server IP not configured.');
    }

   $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $clientName);

    // Simulate: echo "testuser" | ssh -tt ...
   $sshCommand = "echo $cleanName | ssh -tt -i $pemPath -o StrictHostKeyChecking=no $sshUser@$serverIp /home/ec2-user/vpn-cred-create.sh";


    exec($sshCommand . ' 2>&1', $output, $exitCode);

    if ($exitCode === 0) {
        return back()->with('success', "✅ User '$clientName' created successfully.\n" . implode("\n", $output));
    } else {
        return back()->with('error', "❌ Failed to create user (exit code $exitCode).\nOutput:\n" . implode("\n", $output));
    }
}

}
