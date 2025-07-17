<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class LinuxUserCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $server, $username, $password, $publicKey;

    public function __construct($server, $username, $password, $publicKey)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->publicKey = $publicKey;
    }

    public function handle(): void
    {
        $serverIp = config("services.servers.{$this->server}.ip");

        $pemPath  = escapeshellarg(config('services.ec2.key_path'));
        $sshUser  = escapeshellarg(config('services.ec2.user', 'ec2-user'));

        if (!$serverIp) {
            Log::error("❌ LinuxUserCreationJob: Missing IP for server: {$this->server}");
            return;
        }

        // Escape user data
        $escapedUsername  = escapeshellarg($this->username);
        $escapedPassword  = escapeshellarg($this->password);
        $escapedPublicKey = escapeshellarg($this->publicKey);

        // Build SSH command
        $cmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "bash ~/create_linux_user.sh %s %s %s"',
            $pemPath,
            $sshUser,
            $serverIp,
            $escapedUsername,
            $escapedPassword,
            $escapedPublicKey
        );

        exec($cmd . ' 2>&1', $output, $exitCode);

        Log::info('✅ LinuxUserCreationJob executed', [
            'command'   => $cmd,
            'exitCode'  => $exitCode,
            'output'    => $output,
        ]);

        if ($exitCode !== 0) {
            Log::error('❌ Linux user creation failed', ['exitCode' => $exitCode, 'output' => $output]);
        }
    }
}
