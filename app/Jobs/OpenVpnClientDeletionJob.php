<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OpenVpnClientDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $clientName;

    public function __construct(string $clientName)
    {
        $this->clientName = $clientName;
    }

    public function handle(): void
    {
        $task = Task::create([
            'type'     => 'delete_openvpn_user',
            'username' => $this->clientName,
            'server'   => 'openvpn',
            'status'   => 'pending',
            'log'      => '',
        ]);

        $host         = config('services.servers.openvpn.controller_ip');
        $privateKey   = config('services.ubuntu.key_path');
        $sshUser      = config('services.ubuntu.user', 'ubuntu');
        $playbookPath = '/home/ubuntu/ansible-playbooks/vpn-user-delete.yml';

        if (!$host || !$privateKey || !$this->clientName) {
            Log::error('❌ Missing parameters for VPN user deletion.', [
                'host'       => $host,
                'privateKey' => $privateKey,
                'client'     => $this->clientName,
            ]);
            $task->update([
                'status' => 'failed',
                'log'    => 'Missing parameters for VPN user deletion.',
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
            Log::info("✅ VPN client '{$this->clientName}' deleted successfully.", [
                'output' => $logContent,
            ]);
        } else {
            $task->update([
                'status' => 'failed',
                'log'    => json_encode(['stdout_lines' => $output]),
            ]);
            Log::error("❌ VPN client deletion failed for '{$this->clientName}'", [
                'exit_code' => $exitCode,
                'output'    => $logContent,
            ]);
            throw new \RuntimeException("VPN deletion failed for '{$this->clientName}'");
        }
    }

    public function latestOpenVpnDeleteLog()
{
    $task = \App\Models\Task::where('type', 'delete_openvpn_user')
        ->orderByDesc('created_at')
        ->first();

    if (!$task) {
        return response('No VPN delete log found.', 404);
    }

    if (!$task->log) {
        return response("Status: {$task->status}", 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    $lines = [];
    $decoded = json_decode($task->log, true);

    if (is_array($decoded)) {
        foreach ($decoded as $key => $value) {
            if (is_array($value) && isset($value['stdout_lines'])) {
                $lines = $value['stdout_lines'];
                break;
            }
        }

        if (empty($lines)) {
            foreach ($decoded as $key => $value) {
                if (is_array($value) && isset($value['stdout'])) {
                    $lines = explode("\n", $value['stdout']);
                    break;
                }
            }
        }

        if (empty($lines)) {
            foreach ($decoded as $line) {
                if (is_string($line)) {
                    $lines[] = $line;
                }
            }
        }
    }

    if (empty($lines)) {
        $lines = explode("\n", $task->log);
    }

    $filtered = array_filter($lines, fn($line) => str_contains($line, '✅') || str_contains($line, '❌'));

    $output = "Status: {$task->status}\n" . implode("\n", $filtered ?: $lines);

    return response($output, 200, [
        'Content-Type' => 'text/plain',
    ]);
}

}
