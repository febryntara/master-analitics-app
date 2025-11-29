<?php

namespace App\Http\Controllers;

use App\Models\TaskLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskLogController extends Controller
{
    public function progress(TaskLog $taskLog)
    {
        // pastikan data masih fresh
        $taskLog->refresh();

        // format SSE wajib diawali "data: " dan diakhiri 2×LF
        $payload = json_encode([
            'processed_rows' => $taskLog->processed_rows,
            'failed_rows'    => $taskLog->failed_rows,
            'total_rows'     => $taskLog->total_rows,
            'status'         => $taskLog->status,
        ]);

        return response("data: {$payload}\n\n", 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',  // hindari Nginx buffer
        ]);
    }

    public function progressSse($id)
    {
        $taskLog = TaskLog::where('project_id', $id)->latest()->first();
        if (!$taskLog) return response()->json(['message' => 'No task'], 404);

        return new StreamedResponse(function () use ($taskLog) {
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            @ini_set('implicit_flush', true);
            @ob_end_clean();

            $last = 0;
            while (true) {
                $taskLog->refresh();
                $now = $taskLog->processed_rows + $taskLog->failed_rows;

                if ($now !== $last || in_array($taskLog->status, ['completed', 'failed'])) {
                    echo "data: " . json_encode([
                        'processed' => $taskLog->processed_rows,
                        'failed'    => $taskLog->failed_rows,
                        'total'     => $taskLog->total_rows,
                        'status'    => $taskLog->status,
                    ]) . "\n\n";
                    flush();
                    $last = $now;
                }

                if ($taskLog->status === 'completed' || $taskLog->status === 'failed') break;

                // jitter 0,9 – 3,7 detik
                usleep(random_int(900000, 3700000)); // mikro-detik
            }
        }, 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
