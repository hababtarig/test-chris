<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class OpenVpnClientList extends Controller
{
  
public function listOpenVpnUsers()
{
    $vpnClients = Cache::remember('vpn_clients', now()->addMinutes(1), function () {
        $command = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "ls /home/ec2-user/downloads/easy-rsa/easyrsa3/pki/issued | sed \'s/\\.crt$//\'"',
            escapeshellarg(config('services.ec2.key_path')),
            escapeshellarg(config('services.ec2.user', 'ec2')),
            escapeshellarg(config('services.servers.openvpn.public_ip'))
        );
        exec($command, $output, $exitCode);
        return $exitCode === 0 ? $output : [];
    });

    return view('verquin.dashboard', [
       'vpnClients' => $vpnClients,
    ]);
}

}
