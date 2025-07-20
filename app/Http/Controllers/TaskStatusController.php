<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    public function latestDeleteLog()
    {
        return $this->respondWithTaskLog('delete_linux_user');
    }

    public function latestCreateLog()
    {
        return $this->respondWithTaskLog('create_linux_user');
    }

    public function latestOpenVpnCreateLog()
    {
        return $this->respondWithTaskLog('create_openvpn_user');
    }

    public function latestOpenVpnDeleteLog()
    {
        return $this->respondWithTaskLog('delete_openvpn_user');
    }

    private function respondWithTaskLog(string $type)
    {
       $task = Task::where('type', $type)->orderByDesc('created_at')->first();

if (!$task || $task->status === 'pending') {
    return response("Status: Pending");
}


        return response($this->parseTaskLog($task->log, $task->status));
    }

    private function parseTaskLog(?string $log, string $status): string
    {
        $lines = [];

        if ($log) {
            $decoded = json_decode($log, true);

            if (is_array($decoded)) {
                foreach ($decoded as $value) {
                    if (is_array($value)) {
                        if (isset($value['stdout_lines']) && is_array($value['stdout_lines'])) {
                            $lines = $value['stdout_lines'];
                            break;
                        } elseif (isset($value['stdout']) && is_string($value['stdout'])) {
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
        }

        if (empty($lines) && $log) {
            $lines = explode("\n", $log);
        }

        // Filter only echo messages or lines with ✅ / ❌
        $filtered = collect($lines)->filter(function ($line) {
    return str_contains($line, '✅') ||
           str_contains($line, '❌') ||
           stripos($line, 'success') !== false ||
           stripos($line, 'failed') !== false ||
           str_starts_with(trim($line), 'echo ');
})->all();


        $statusLine = match ($status) {
            'success' => 'Status: ✅ Success',
            'failed'  => 'Status: ❌ Failed',
            'pending' => 'Status: Pending',
            default   => "Status: {$status}"
        };

        return $statusLine . "\n" . implode("\n", $filtered ?: []);
    }
}
