<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $host         = config('services.servers.openvpn.controller_ip');
        $privateKey   = config('services.ubuntu.key_path');
        $sshUser      = config('services.ubuntu.user', 'ubuntu');
        $playbookPath = '/home/ubuntu/ansible-playbooks/vpn-user-create.yml';

        if (!$host || !$privateKey || !$this->clientName) {
            Log::error('❌ Missing parameters for VPN user creation.', [
                'host'       => $host,
                'privateKey' => $privateKey,
                'client'     => $this->clientName,
            ]);
            return;
        }

        $command = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "ansible-playbook %s -e \'client_name=%s\'"',
            escapeshellarg($privateKey),
            escapeshellarg($sshUser),
            escapeshellarg($host),
            escapeshellarg($playbookPath),
            escapeshellarg($this->clientName)
        );

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode === 0) {
            Log::info("✅ VPN client '{$this->clientName}' created successfully.", [
                'output' => implode("\n", $output),
            ]);
        } else {
            Log::error("❌ VPN client creation failed for '{$this->clientName}'", [
                'exit_code' => $exitCode,
                'output'    => implode("\n", $output),
            ]);

            throw new \RuntimeException("VPN creation failed for '{$this->clientName}'");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("❌ Job failed for VPN client '{$this->clientName}'", [
            'error' => $exception->getMessage(),
        ]);
    }
}
