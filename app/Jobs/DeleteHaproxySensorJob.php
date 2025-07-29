<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

class DeleteHaproxySensorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $sensorName;
    protected string $frontendPort;
    protected string $backendIp;

    public function __construct(string $sensorName, string $frontendPort, string $backendIp)
    {
        $this->sensorName   = $sensorName;
        $this->frontendPort = $frontendPort;
        $this->backendIp    = $backendIp;
    }

    public function handle(): void
    {
        $task = Task::create([
            'type'     => 'delete_haproxy_sensor',
            'server'   => 'haproxy',
            'username' => $this->sensorName,
            'status'   => 'pending',
        ]);

        $controllerIp = config('services.servers.haproxy.controller_ip');
        $pemPath      = config('services.ubuntu.key_path');
        $sshUser      = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp) {
            $task->status = 'failed';
            $task->log    = "Missing controller IP for HAProxy server.";
            $task->save();
            Log::error("❌ DeleteHaproxySensorJob: Missing controller IP");
            return;
        }

        $escapedName  = escapeshellarg($this->sensorName);
        $escapedFPort = escapeshellarg($this->frontendPort);
        $escapedBIP   = escapeshellarg($this->backendIp);

        $ansibleCmd = sprintf(
            'ansible-playbook /home/ubuntu/ansible-playbooks/delete-sensor.yml -e sensor_name=%s -e frontend_port=%s -e backend_ip=%s',
            $escapedName,
            $escapedFPort,
            $escapedBIP
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

            Log::info('✅ DeleteHaproxySensorJob executed', [
                'command'  => $sshCmd,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        } catch (\Throwable $e) {
            $task->status = 'failed';
            $task->log    = $e->getMessage();
            $task->save();

            Log::error('❌ DeleteHaproxySensorJob failed with exception', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
