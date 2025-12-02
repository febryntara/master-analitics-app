<?php

namespace App\Jobs;

use App\Events\BatchProcessingFinished;
use App\Models\ProjectData;
use App\Models\ProcessedData;
use App\Models\TaskLog;
use App\Models\ApiLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProcessProjectData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ProjectData $projectData;
    protected int $taskLogId;

    /**
     * Create a new job instance.
     */
    public function __construct(ProjectData $projectData, int $taskLogId)
    {
        $this->projectData = $projectData;
        $this->taskLogId = $taskLogId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::debug('JOB-START', ['pd_id' => $this->projectData->id, 'tl_id' => $this->taskLogId]);
        $this->projectData->update(['status' => 'processing']);

        $startTime = microtime(true);

        try {
            // Kirim data ke Flask
            $response = Http::post(config('services.flask.url') . '/preprocess', [
                'raw_id' => $this->projectData->raw_id,
                'raw_text' => $this->projectData->raw_text,
            ]);

            $durationMs = (microtime(true) - $startTime) * 1000;

            // Catat API Log
            ApiLog::create([
                'endpoint' => '/preprocess',
                'method' => 'POST',
                'request_payload' => json_encode([
                    'raw_id' => $this->projectData->raw_id,
                    'raw_text' => $this->projectData->raw_text,
                ]),
                'response_payload' => $response->body(),
                'status_code' => $response->status(),
                'duration_ms' => (int)$durationMs,
                'project_id' => $this->projectData->project_id,
            ]);

            if ($response->successful()) {
                $resData = $response->json();

                // Simpan ke ProcessedData
                ProcessedData::create([
                    'project_data_id' => $this->projectData->id,
                    'cleaned_text' => $resData['cleaned_text'] ?? '',
                    'sentiment' => $resData['sentiment'],
                    'confidence_score' => $resData['confidence_score'] ?? 0,
                    'preprocessing_time_ms' => $resData['preprocessing_time_ms'] ?? (int)$durationMs,
                    'model_version' => $resData['model_version'] ?? null,
                ]);

                // Update ProjectData status
                $this->projectData->update(['status' => 'done']);
                $col = 'processed_rows';
            } else {
                $this->projectData->update([
                    'status' => 'error',
                    'error_message' => 'Flask API error: ' . $response->body(),
                ]);
                $col = 'failed_rows';
                \Log::debug('JOB-ERROR', ['pd_id' => $this->projectData->id, 'tl_id' => $this->taskLogId]);
            }
        } catch (\Exception $e) {
            $this->projectData->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
            $col = 'failed_rows';
            \Log::debug('JOB-FAILED', ['pd_id' => $this->projectData->id, 'tl_id' => $this->taskLogId]);
        }

        /* ========== SEMOR & EVENT LAST JOB ========== */
        DB::transaction(function () use ($col) {
            // 1. increment dulu
            TaskLog::where('id', $this->taskLogId)->increment($col);
            \Log::debug('JOB-INCREMENTED', ['pd_id' => $this->projectData->id]);
            // 2. ambil row-nya (lock) dan hitung di PHP
            $task = TaskLog::where('id', $this->taskLogId)
                ->lockForUpdate()
                ->first();

            if (! $task) return;

            $done = $task->processed_rows + $task->failed_rows;
            if ($done >= $task->total_rows && $task->status !== 'completed') {
                $task->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);

                event(new BatchProcessingFinished($task->project_id, $this->taskLogId));
            }
        });
    }
}
