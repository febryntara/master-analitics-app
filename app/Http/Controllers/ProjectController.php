<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TaskLog;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $data = [
            'projects' => Project::latest()->get(),
        ];
        return view('project.index', $data);
    }

    public function create()
    {
        $data = [];
        return view('project.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'raw_text_label' => 'required|string|max:255',
            'raw_id_label' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'There were some problems with your input.')
                ->withInput();
        }

        $validated = $validator->validated();

        $is_created = Project::create([
            'user_id' => auth()->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'raw_text_label' => $validated['raw_text_label'],
            'raw_id_label' => $validated['raw_id_label'],
            'status' => 'pending',
        ]);

        if ($is_created) {
            return redirect()->route('projects.index')
                ->with('success', 'Project created successfully.');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to create project. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        $projectDataCount = $project->data()->count();
        $taskLogActive = TaskLog::where('project_id', $project->id)
            ->where('status', 'running')
            ->latest()
            ->first();
        $total_task_rows = TaskLog::where('project_id', $project->id)
            ->sum('total_rows');
        $canInputFile = TaskLog::where('project_id', $project->id)
            ->where('status', 'running')->orWhere('status', 'pending')
            ->doesntExist();

        $canAnalyze = is_null($taskLogActive) && $project->status != 'finished' && $projectDataCount > $total_task_rows && $projectDataCount > 0;

        $data = [
            'project' => Project::findOrFail($id),
            'projectDataCount' => $projectDataCount,
            'taskLog' => $taskLogActive,
            'canAnalyze' => $canAnalyze,
            'canInputFile' => $canInputFile,
        ];

        return view('project.show', $data);
    }

    public function edit($id)
    {
        $data = [
            'project' => Project::findOrFail($id),
        ];
        return view('project.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'raw_text_label' => 'required|string|max:255',
            'raw_id_label' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'There were some problems with your input.')
                ->withInput();
        }

        $validated = $validator->validated();

        $project = Project::findOrFail($id);
        $project->name = $validated['name'];
        $project->description = $validated['description'] ?? null;
        $project->raw_text_label = $validated['raw_text_label'];
        $project->raw_id_label = $validated['raw_id_label'];
        $is_updated = $project->save();

        if ($is_updated) {
            return redirect()->route('projects.show', ['project' => $project])
                ->with('success', 'Project updated successfully.');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to update project. Please try again.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $is_deleted = $project->delete();

        if ($is_deleted) {
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to delete project. Please try again.');
        }
    }

    public function finishAnalyzing($id)
    {
        $project = Project::findOrFail($id);
        $project->status = 'finished';
        $is_updated = $project->save();

        if ($is_updated) {
            return redirect()->route('projects.show', ['project' => $project])
                ->with('success', 'Project analysis marked as finished.');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to mark project as finished. Please try again.');
        }
    }

    public function deleteRawData($id)
    {
        $project = Project::findOrFail($id);
        $deletedCount = $project->data()->count();
        $project->data()->get()->each->delete();
        $project->taskLogs()->delete();
        $project->status = 'pending';
        $project->save();

        return redirect()->route('projects.show', ['project' => $project])
            ->with('success', "$deletedCount raw data entries deleted successfully.");
    }

    public function setSession($projectId)
    {
        session()->flash('success', 'Project processing completed!');
        return response()->json(['status' => 'ok']);
    }
}
