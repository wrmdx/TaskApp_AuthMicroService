<?php

namespace App\Http\Controllers;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use Illuminate\Http\Request;
use App\Service\TaskService;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\displayUsersName;
use App\Http\Resources\displayProjectsName;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $status = $request->status;
        $priority = $request->priority;
        $sortField = $request->input("sorted", "created_at");
        $direction = $request->input("direction", "desc");
        $taskInstance = new TaskService();
        $tasks = $taskInstance->transform($name, $status, $sortField, $direction, $priority);
        return inertia("Tasks/Index", ["tasks" => TaskResource::collection($tasks), "nameQuery" => $name, "statusQuery" => $status, "sortField" => $sortField, "direction" => $direction, "priorityQuery" => $priority,"success" => session("success")]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = displayProjectsName::collection(Project::all());
        $users = displayUsersName::collection(User::all());
        return inertia("Tasks/Create", [
            "projects"=> $projects,
            "users"=>$users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data["created_by"] = auth()->id();
        $data["updated_by"] = auth()->id();
        if ($request->hasFile("image_path")) {
            $data["image_path"] = "storage/" . $request->file('image_path')->store('images', 'public');
        }
        Task::create($data);
        return to_route("task.index")->with("success", "Task Created Successfully");
    }

    public function show(Task $task) {
        $taskResource = new TaskResource($task);
    return inertia("Tasks/Show",["task"=>$taskResource]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $projects = displayProjectsName::collection(Project::all());
        $users = displayUsersName::collection(User::all());
        return inertia("Tasks/Edit",["dataSent"=>
            [$projects, $users,$task]]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        $data["updated_by"] = auth()->id();
        if ($request->hasFile("image_path")) {
            if($task->image_path) {
                Storage::disk("public")->delete($task->image_path);
            }
            $data["image_path"] = "storage/" . $request->file('image_path')->store('images', 'public');
        }
        $task->update($data);
        return to_route("task.index")->with("success","Task updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $name = $task->name;
        $task->delete();
        return to_route("task.index")->with("success","Task \"$name\" Deleted Successfully");
    }

    public function my_tasks(Request $request) {
        $user = auth()->id();
        $name = $request->name;
        $status = $request->status;
        $priority = $request->priority;
        $sortField = $request->input("sorted", "created_at");
        $direction = $request->input("direction", "desc");
        $taskInstance = new TaskService();
        $tasks = $taskInstance->transformMyTasks($user,$name, $status, $sortField, $direction, $priority);
        return inertia("Tasks/Index", ["tasks" => TaskResource::collection($tasks), "nameQuery" => $name, "statusQuery" => $status, "sortField" => $sortField, "direction" => $direction, "priorityQuery" => $priority,"success" => session("success")]);
    }
}
