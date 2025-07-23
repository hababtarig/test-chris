<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteLinuxUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $server;
    protected string $username;
    protected int $taskId;

    public function __construct(string $server, string $username, int $taskId)
    {
        $this->server = $server;
        $this->username = $username;
        $this->taskId = $taskId;
    }

    public function handle(): void
    {
        $task = Task::find($this->taskId);
        if (!$task) {
            Log::error("❌ DeleteLinuxUserJob: Task not found", ['task_id' => $this->taskId]);
            return;
        }

        $controllerIp = config("services.servers.{$this->server}.controller_ip");
        $targetIp     = config("services.servers.{$this->server}.ip");

        $pemPath = config('services.ubuntu.key_path');
        $sshUser = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp || !$targetIp) {
            $task->status = 'failed';
            $task->log = "Missing IP(s) for server: {$this->server}";
            $task->save();

            Log::error("❌ DeleteLinuxUserJob: Missing IP(s)", [
                'controller_ip' => $controllerIp,
                'target_ip'     => $targetIp,
            ]);
            return;
        }

        // Build Ansible command
        $escapedUsername = escapeshellarg($this->username);
    $ansibleHostGroup = "{$this->server}_servers";

    $ansibleCmd = sprintf(
        'ansible-playbook -i /etc/ansible/hosts /home/ubuntu/ansible-playbooks/delete-linux-user.yml --limit %s --extra-vars \'username=%s\'',
        escapeshellarg($ansibleHostGroup),
        $escapedUsername
    );

        $sshCmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "%s"',
            escapeshellarg($pemPath),
            $sshUser,
            escapeshellarg($controllerIp),
            $ansibleCmd
        );

        $output = [];
        $exitCode = 0;

        try {
           exec($sshCmd . ' 2>&1', $output, $exitCode);

$task->status = ($exitCode === 0) ? 'success' : 'failed';
$task->log = implode("\n", $output);
$task->save();


            Log::info('✅ DeleteLinuxUserJob executed', [
                'command'   => $sshCmd,
                'exitCode'  => $exitCode,
                'output'    => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log = $e->getMessage();
            $task->save();

            Log::error('❌ DeleteLinuxUserJob failed with exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
