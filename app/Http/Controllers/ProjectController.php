<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Entity\Project;
use App\Entity\Task;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::query();
        $name = $request->name;
        $status = $request->status;
        $sortField = $request->input("sorted", "created_at");
        $direction = $request->input("direction", "desc");
        if ($name) {
            $query->where("name", "like", "%" . $name . "%");
        }
        if ($status) {
            $query->where("status", $status);
        }
        $projects = $query->orderBy($sortField, $direction)->paginate(10);
        $projectResource = ProjectResource::collection($projects);
        return inertia("Projects/Index", ["projects" => $projectResource, "nameQuery" => $name, "statusQuery" => $status, "sortField" => $sortField, "direction" => $direction,"success"=>session("success")]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia("Projects/Create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $data["created_by"] = auth()->id();
        $data["updated_by"] = auth()->id();
        if ($request->hasFile('image_path')) {
                $data["image_path"] = "storage/". $request->file('image_path')->store('images','public');
        }
        Project::create($data);
        return redirect()->route("project.index")->with("success","Project Created Successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        $query = $project->tasks();

        $queryParam["name"] = $request->name;
        $queryParam["status"] = $request->status;
        $queryParam["sorted"] = $request->input("sorted", "due_date");
        $queryParam["direction"] = $request->input("direction", "desc");
        if ($queryParam["name"]) {
            $query->where("name", "like", "%" . $queryParam["name"] . "%");
        }
        if ($queryParam["status"]) {
            $query->where("status", $queryParam["status"]);
        }
        $tasks = $query->orderBy( $queryParam["sorted"],  $queryParam["direction"])->paginate(10);
        return inertia("Projects/Show", ["project" => new ProjectResource($project), "tasks" => TaskResource::collection($tasks), "queryParams" => $queryParam]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return inertia("Projects/Edit",$project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $data["updated_by"] = auth()->id();
        if ($request->hasFile('image_path')) {
            if($project->image_path) {
                Storage::disk("public")->delete($project->image_path);
            }
                $data["image_path"] = $request->file('image_path')->store('images','public');
        }
        $project->update($data);
        return redirect()->route('project.index')->with('success', 'Project Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $name = $project->name;
        $project->delete();
        if($project->image_path) {
            Storage::disk("public")->delete($project->image_path);
        }
        return to_route("project.index")->with("success","Project $name Deleted Successfully");
    }
}
