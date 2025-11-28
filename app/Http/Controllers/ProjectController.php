<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        $data = [
            'project' => Project::findOrFail($id),
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
}
