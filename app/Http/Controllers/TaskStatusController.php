<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{public function latestDeleteLog()
{
    $task = Task::where('type', 'delete_linux_user')
        ->orderByDesc('created_at')
        ->first();

    if (!$task) {
        return response('No task found.');
    }

    if (!$task->log) {
        // No log yet → probably still pending
        return response("Status: {$task->status}");
    }

    $lines = [];

    // Try to decode the log JSON
    $decoded = json_decode($task->log, true);

    if (is_array($decoded)) {
        // look for nested stdout_lines or stdout
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
    }

    // fallback: maybe log is plain text
    if (empty($lines)) {
        $lines = explode("\n", $task->log);
    }

    // Filter only lines with ✅ or ❌
    $filtered = array_filter($lines, function($line) {
        return str_contains($line, '❌') || str_contains($line, '✅');
    });

    $output = "Status: {$task->status}";
    if ($filtered) {
        $output .= "\n" . implode("\n", $filtered);
    }

    return response($output);
}
public function latestCreateLog()
{
    $task = Task::where('type', 'create_linux_user')
        ->orderByDesc('created_at')
        ->first();

    if (!$task) {
        return response('No task found.');
    }

    if (!$task->log) {
        return response("Status: {$task->status}");
    }

    $lines = [];

    // Try to decode JSON
    $decoded = json_decode($task->log, true);

    if (is_array($decoded)) {
        // check for stdout_lines
        foreach ($decoded as $key => $value) {
            if (is_array($value) && isset($value['stdout_lines'])) {
                $lines = $value['stdout_lines'];
                break;
            }
        }

        // fallback to stdout
        if (empty($lines)) {
            foreach ($decoded as $key => $value) {
                if (is_array($value) && isset($value['stdout'])) {
                    $lines = explode("\n", $value['stdout']);
                    break;
                }
            }
        }

        // fallback to top-level strings
        if (empty($lines)) {
            foreach ($decoded as $line) {
                if (is_string($line)) {
                    $lines[] = $line;
                }
            }
        }
    }

    // fallback: treat raw log as plain text
    if (empty($lines)) {
        $lines = explode("\n", $task->log);
    }

    // optional filter ✅ or ❌ only:
    $filtered = array_filter($lines, fn($line) => str_contains($line, '✅') || str_contains($line, '❌'));

    $output = "Status: {$task->status}";
    $output .= "\n" . implode("\n", $filtered ?: $lines); // fallback to all if filtered is empty

    return response($output);
}
public function latestOpenVpnCreateLog()
{
    $task = Task::where('type', 'create_openvpn_user')  // or whatever type you store for OpenVPN user creation
        ->orderByDesc('created_at')
        ->first();

    if (!$task) {
        return response('No task found.');
    }

    if (!$task->log) {
        return response("Status: {$task->status}");
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

    // Filter lines containing ✅ or ❌ only:
    $filtered = array_filter($lines, fn($line) => str_contains($line, '✅') || str_contains($line, '❌'));

    $output = "Status: {$task->status}";
    $output .= "\n" . implode("\n", $filtered ?: $lines);

    return response($output);
}


}
