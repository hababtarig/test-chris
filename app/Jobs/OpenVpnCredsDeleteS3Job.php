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

    public function __construct(?string $certName = '', ?string $keyName = '', ?string $ovpnName = '')
    {
        $this->certName = $certName ?? '';
        $this->keyName  = $keyName ?? '';
        $this->ovpnName = $ovpnName ?? '';
    }

    public function handle(): void
    {
        if (empty($this->certName) && empty($this->keyName) && empty($this->ovpnName)) {
            Log::error('❌ OpenVpnCredsDeleteS3Job: No filenames provided');
            return;
        }

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

        $args = [];
        if ($this->certName) $args[] = '-e cert_name=' . escapeshellarg($this->certName);
        if ($this->keyName)  $args[] = '-e key_name=' . escapeshellarg($this->keyName);
        if ($this->ovpnName) $args[] = '-e ovpn_name=' . escapeshellarg($this->ovpnName);

        $ansibleCmd = 'ansible-playbook /home/ubuntu/ansible-playbooks/s3-vpn-creds-delete.yml ' . implode(' ', $args);

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
