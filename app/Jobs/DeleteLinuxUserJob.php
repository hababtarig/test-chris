<?php

namespace App\Jobs;

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

    public function __construct(string $server, string $username)
    {
        $this->server = $server;
        $this->username = $username;
    }

    public function handle(): void
    {
        $controllerIp = config("services.servers.{$this->server}.controller_ip");
        $targetIp     = config("services.servers.{$this->server}.ip");

        $pemPath = config('services.ubuntu.key_path');
        $sshUser = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));

        if (!$controllerIp || !$targetIp) {
            Log::error("❌ DeleteLinuxUserJob: Missing IP(s) for server: {$this->server}", [
                'controller_ip' => $controllerIp,
                'target_ip'     => $targetIp,
            ]);
            return;
        }

        // Construct Ansible command
    $ansibleCmd = sprintf(
    "ansible-playbook -i /etc/ansible/hosts /home/ubuntu/ansible-playbooks/delete-linux-user.yml --extra-vars 'username=%s'",
    $this->username
);

        // SSH into controller to run playbook
        $sshCmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "%s"',
            $pemPath,
            $sshUser,
            $controllerIp,
            $ansibleCmd
        );

        exec($sshCmd . ' 2>&1', $output, $exitCode);

        Log::info('✅ DeleteLinuxUserJob executed', [
            'command'   => $sshCmd,
            'exitCode'  => $exitCode,
            'output'    => $output,
        ]);

        if ($exitCode !== 0) {
            Log::error('❌ Failed to delete Linux user', [
                'username' => $this->username,
                'exitCode' => $exitCode,
                'output'   => $output,
            ]);
        }
    }
}
