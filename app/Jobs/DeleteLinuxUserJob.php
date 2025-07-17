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

    protected string $host;
    protected string $privateKey;
    protected string $sshUser;
    protected string $username;

    public function __construct(string $host, string $privateKey, string $sshUser, string $username)
    {
        $this->host = $host;
        $this->privateKey = $privateKey;
        $this->sshUser = $sshUser;
        $this->username = $username;
    }

    public function handle(): void
    {
        $remoteScript = 'delete_linux_user.sh';

        if (!$this->host || !$this->privateKey || !$this->sshUser || !$this->username) {
            Log::error("Missing Linux user deletion job parameters", [
                'host' => $this->host,
                'privateKey' => $this->privateKey,
                'sshUser' => $this->sshUser,
                'username' => $this->username,
            ]);
            return;
        }

        $cmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "bash ~/%s %s"',
            escapeshellarg($this->privateKey),
            escapeshellarg($this->sshUser),
            escapeshellarg($this->host),
            escapeshellarg($remoteScript),
            escapeshellarg($this->username)
        );

        exec($cmd . ' 2>&1', $output, $exitCode);

        Log::info("Linux user deletion job result", [
            'command' => $cmd,
            'exit' => $exitCode,
            'output' => $output,
        ]);
    }
}
