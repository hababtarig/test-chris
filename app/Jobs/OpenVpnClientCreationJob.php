<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OpenVpnClientCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $clientName;

    public function __construct(string $clientName)
    {
        $this->clientName = $clientName;
    }

    public function handle(): void
    {
        $host         = config('services.servers.openvpn.ip');
        $privateKey   = config('services.ec2.key_path');
        $sshUser      = config('services.ec2.user', 'ec2-user');
        $remoteScript = 'vpn-cred-create.sh';

        if (!$host || !$privateKey || !$this->clientName) {
            Log::error("❌ Missing parameters for VPN job", [
                'host'       => $host,
                'privateKey' => $privateKey,
                'client'     => $this->clientName,
            ]);
            return;
        }

        $command = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "bash ~/%s %s"',
            escapeshellarg($privateKey),
            escapeshellarg($sshUser),
            escapeshellarg($host),
            escapeshellarg($remoteScript),
            escapeshellarg($this->clientName)
        );

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode === 0) {
    Log::info("✅ VPN user '{$this->clientName}' created successfully", [
        'output' => implode("\n", $output)
    ]);
} else {
    $errorMessage = "❌ VPN user '{$this->clientName}' creation failed";
    Log::error($errorMessage, [
        'exit'   => $exitCode,
        'output' => implode("\n", $output)
    ]);

    // ✅ THROW EXCEPTION HERE
    throw new \RuntimeException($errorMessage . ": " . implode("\n", $output));
}

    }
}
