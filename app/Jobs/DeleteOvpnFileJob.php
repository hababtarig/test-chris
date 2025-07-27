<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class DeleteOvpnFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $server;
    protected string $clientName;
    protected int $taskId;

    public function __construct(string $server, string $clientName, int $taskId)
    {
        $this->server = $server;
        $this->clientName = $clientName;
        $this->taskId = $taskId;
    }

    public function handle(): void
    {
        $task = Task::find($this->taskId);
        if (!$task) {
            Log::error("❌ DeleteOvpnFileJob: Task not found", ['task_id' => $this->taskId]);
            return;
        }

        $controllerIp = config("services.servers.{$this->server}.controller_ip");
        $pemPath = config('services.ubuntu.key_path');
        $sshUser = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp) {
            $task->status = 'failed';
            $task->log = "Missing controller IP for server: {$this->server}";
            $task->save();
            Log::error("❌ DeleteOvpnFileJob: Missing controller IP", ['server' => $this->server]);
            return;
        }

        $escapedClientName = escapeshellarg($this->clientName);
        $ansibleCmd = sprintf(
            'ansible-playbook /home/ubuntu/ansible-playbooks/ovpn-file-delete.yml -e client_name=%s',
            $escapedClientName
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

            $task->status = ($exitCode === 0) ? 'success' : 'failed';
            $task->log = implode("\n", $output);
            $task->save();

            Log::info('✅ DeleteOvpnFileJob executed', [
                'command'  => $sshCmd,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log = $e->getMessage();
            $task->save();

            Log::error('❌ DeleteOvpnFileJob failed with exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
