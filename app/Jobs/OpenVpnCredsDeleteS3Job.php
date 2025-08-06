<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

class OpenVpnCredsDeleteS3Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $certName;
    protected string $keyName;
    protected string $ovpnName;

    public function __construct(string $certName, string $keyName, string $ovpnName)
    {
        $this->certName = $certName;
        $this->keyName = $keyName;
        $this->ovpnName = $ovpnName;
    }

    public function handle(): void
    {
        $task = Task::create([
            'type'     => 'openvpn_creds_delete_s3',
            'server'   => 'vpn',
            'username' => $this->certName,
            'status'   => 'pending',
        ]);

        $controllerIp = config('services.servers.openvpn.controller_ip');
        $pemPath      = config('services.ubuntu.key_path');
        $sshUser      = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp) {
            $task->status = 'failed';
            $task->log    = 'Missing controller IP for VPN server.';
            $task->save();
            Log::error('❌ OpenVpnCredsDeleteS3Job: Missing controller IP');
            return;
        }

        $ansibleCmd = sprintf(
            'ansible-playbook /home/ubuntu/ansible-playbooks/s3-vpn-creds-delete.yml -e cert_name=%s -e key_name=%s -e ovpn_name=%s',
            escapeshellarg($this->certName),
            escapeshellarg($this->keyName),
            escapeshellarg($this->ovpnName)
        );

        $sshCmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "%s"',
            escapeshellarg($pemPath),
            $sshUser,
            $controllerIp,
            $ansibleCmd
        );

        try {
            $output = [];
            $exitCode = 0;

            exec($sshCmd . ' 2>&1', $output, $exitCode);

            $task->status = $exitCode === 0 ? 'success' : 'failed';
            $task->log    = implode("\n", $output);
            $task->save();

            Log::info('✅ OpenVpnCredsDeleteS3Job executed', [
                'command'  => $sshCmd,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log    = $e->getMessage();
            $task->save();

            Log::error('❌ OpenVpnCredsDeleteS3Job failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
