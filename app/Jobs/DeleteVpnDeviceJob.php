<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class DeleteVpnDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $deviceName;
    protected string $deviceIp;
    protected string $deviceSubnet;

    public function __construct(string $deviceName, string $deviceIp, string $deviceSubnet)
    {
        $this->deviceName   = $deviceName;
        $this->deviceIp     = $deviceIp;
        $this->deviceSubnet = $deviceSubnet;
    }

    public function handle(): void
    {
        $task = Task::create([
            'type'     => 'delete_openvpn_device_creds',
            'server'   => 'openvpn',
            'username' => $this->deviceName,
            'status'   => 'pending',
        ]);

        $controllerIp = config('services.servers.openvpn.controller_ip');
        $pemPath      = config('services.ubuntu.key_path');
        $sshUser      = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp) {
            $task->status = 'failed';
            $task->log    = "Missing controller IP for OpenVPN server.";
            $task->save();
            Log::error("❌ DeleteVpnDeviceJob: Missing controller IP");
            return;
        }


        $escapedName   = escapeshellarg($this->deviceName);
        $escapedIp     = escapeshellarg($this->deviceIp);
        $escapedSubnet = escapeshellarg($this->deviceSubnet);

        // ansible command
        $ansibleCmd = sprintf(
            'ansible-playbook /home/ubuntu/ansible-playbooks/vpn-device-creds-delete.yml -e device_name=%s -e device_ip=%s -e device_subnet=%s',
            $escapedName,
            $escapedIp,
            $escapedSubnet
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

            Log::info('✅ DeleteVpnDeviceJob executed', [
                'command'  => $sshCmd,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log    = $e->getMessage();
            $task->save();

            Log::error('❌ DeleteVpnDeviceJob failed with exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
