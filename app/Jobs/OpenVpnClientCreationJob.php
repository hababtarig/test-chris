<?php

namespace App\Jobs;

use App\Models\Task; 
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
        // Create or get existing Task record
      $task = Task::create([
    'type'    => 'create_openvpn_user',
    'username'=> $this->clientName,
    'server'  => 'openvpn',
    'status'  => 'pending',
    'log'     => '',
]);


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
            // Update task status and exit early
            $task->update([
                'status' => 'failed',
                'log'    => 'Missing parameters for VPN user creation.',
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

        $logContent = implode("\n", $output);

        if ($exitCode === 0) {
            $task->update([
                'status' => 'success',
                'log'    => json_encode(['stdout_lines' => $output]),
            ]);
            Log::info("✅ VPN client '{$this->clientName}' created successfully.", [
                'output' => $logContent,
            ]);
        } else {
            $task->update([
                'status' => 'failed',
                'log'    => json_encode(['stdout_lines' => $output]),
            ]);
            Log::error("❌ VPN client creation failed for '{$this->clientName}'", [
                'exit_code' => $exitCode,
                'output'    => $logContent,
            ]);
            throw new \RuntimeException("VPN creation failed for '{$this->clientName}'");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("❌ Job failed for VPN client '{$this->clientName}'", [
            'error' => $exception->getMessage(),
        ]);

        // Optionally update the Task status here too
        $task = Task::where('type', 'create_openvpn_user')
                    ->where('username', $this->clientName)
                    ->latest()
                    ->first();

        if ($task) {
            $task->update([
                'status' => 'failed',
                'log' => $task->log . "\nJob failed with error: " . $exception->getMessage(),
            ]);
        }
    }
}
