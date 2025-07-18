<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LinuxUserCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server, $username, $password, $publicKey;

    public function __construct($server, $username, $password, $publicKey)
    {
        $this->server    = $server;
        $this->username  = $username;
        $this->password  = $password;
        $this->publicKey = $publicKey;
    }

    public function handle(): void
    {
        // Controller = Ubuntu (Ansible) box
        $controllerIp = config("services.servers.{$this->server}.controller_ip");
        $targetIp     = config("services.servers.{$this->server}.ip");

       //$pemPath = base_path(config('services.ubuntu.key_path')); 
       $pemPath = config('services.ubuntu.key_path'); 
       $sshUser = escapeshellarg(config('services.ubuntu.user', 'ubuntu'));


        if (!$controllerIp || !$targetIp) {
            Log::error("❌ LinuxUserCreationJob: Missing IP(s) for server: {$this->server}", [
                'controller_ip' => $controllerIp,
                'target_ip'     => $targetIp,
            ]);
            return;
        }

        $escapedUsername  = escapeshellarg($this->username);
        $escapedPassword  = escapeshellarg($this->password);
        $escapedPublicKey = escapeshellarg($this->publicKey);

        // Construct the Ansible command to run on the Ubuntu Ansible host
        // REMOVE escapeshellcmd here — it's escaping incorrectly for Ansible
$ansibleCmd = sprintf(
    'ansible-playbook -i /etc/ansible/hosts /home/ubuntu/ansible-playbooks/create-linux-user.yml --extra-vars \'username=%s password=%s pubkey=%s\'', 
    $this->username,
    $this->password,
    $this->publicKey
);


        // Full SSH command to run on the Ubuntu controller
        $sshCmd = sprintf(
            'ssh -i %s -o StrictHostKeyChecking=no %s@%s "%s"',
            $pemPath,
            $sshUser,
            $controllerIp,
            $ansibleCmd
        );

        exec($sshCmd . ' 2>&1', $output, $exitCode);

        Log::info('✅ LinuxUserCreationJob executed', [
            'command'   => $sshCmd,
            'exitCode'  => $exitCode,
            'output'    => $output,
        ]);

        if ($exitCode !== 0) {
            Log::error('❌ Linux user creation failed', ['exitCode' => $exitCode, 'output' => $output]);
        }
    }
}
