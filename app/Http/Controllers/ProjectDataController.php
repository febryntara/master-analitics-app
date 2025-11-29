<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProjectData;
use App\Models\Project;
use App\Models\ProjectData;
use App\Models\TaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectDataController extends Controller
{
    public function uploadBatch(Request $request, $projectId)
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'data_json_input' => 'required|file|mimes:json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $project = Project::findOrFail($projectId);

        // Ambil file dan decode JSON
        $jsonContent = file_get_contents($request->file('data_json_input')->getRealPath());
        $dataArray = json_decode($jsonContent, true);

        if (!is_array($dataArray)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File JSON tidak valid.',
            ], 422);
        }

        $insertedCount = 0;

        foreach ($dataArray as $item) {
            // Ambil field sesuai mapping project
            $rawId = $item[$project->raw_id_label] ?? null;
            $rawText = $item[$project->raw_text_label] ?? null;

            // Skip jika raw_id atau raw_text kosong
            if (!$rawId || !$rawText) continue;

            // Cek redudansi (sudah ada di project_data)
            $exists = ProjectData::where('project_id', $project->id)
                ->where('raw_id', $rawId)
                ->exists();
            if ($exists) continue;

            // Simpan ke ProjectData
            ProjectData::create([
                'project_id' => $project->id,
                'raw_id' => $rawId,
                'raw_text' => $rawText,
                'status' => 'pending',
            ]);

            $insertedCount++;
        }

        return redirect()->route('projects.show', ['project' => $project->id])
            ->with('success', "$insertedCount data berhasil diupload.");
    }

    public function startBatchProcessing($projectId)
    {
        $project = Project::findOrFail($projectId);
        $dataItems = $project->data()->where('status', 'pending')->get();

        if ($dataItems->isEmpty()) {
            return redirect()->route('projects.show', ['project' => $project])->with([
                'error' => 'Tidak ada data pending untuk diproses.'
            ]);
        }

        // Buat TaskLog
        $taskLog = TaskLog::create([
            'project_id' => $project->id,
            'total_rows' => $dataItems->count(),
            'processed_rows' => 0,
            'failed_rows' => 0,
            'status' => 'running',
            'started_at' => now(),
        ]);

        // Dispatch job per item
        foreach ($dataItems as $item) {
            ProcessProjectData::dispatch($item, $taskLog->id);
        }

        $project->update(['status' => 'processing']);

        return redirect()->route('projects.show', ['project' => $project])
            ->with('task_log_id', $taskLog->id)
            ->with('success', 'Batch processing dimulai.');
    }
}
