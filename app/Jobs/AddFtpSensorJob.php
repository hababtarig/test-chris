<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

class AddFtpSensorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $sensorName;

    public function __construct(string $sensorName)
    {
        $this->sensorName = $sensorName;
    }

    public function handle(): void
    {
        $task = Task::create([
            'type'     => 'add_ftp_sensor',
            'server'   => 'ftp',
            'username' => $this->sensorName,
            'status'   => 'pending',
        ]);

        $controllerIp = config('services.servers.ftp.controller_ip');
        $pemPath      = config('services.ubuntu.key_path');
        $sshUser      = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp) {
            $task->status = 'failed';
            $task->log    = "Missing controller IP for FTP server.";
            $task->save();
            Log::error("❌ AddFtpSensorJob: Missing controller IP");
            return;
        }

        $escapedName = escapeshellarg($this->sensorName);

        $ansibleCmd = sprintf(
            'ansible-playbook /home/ubuntu/ansible-playbooks/ftp-alerts-create.yml -e sensor_name=%s',
            $escapedName
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

            Log::info('✅ AddFtpSensorJob executed', [
                'command'  => $sshCmd,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log    = $e->getMessage();
            $task->save();

            Log::error('❌ AddFtpSensorJob failed with exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
