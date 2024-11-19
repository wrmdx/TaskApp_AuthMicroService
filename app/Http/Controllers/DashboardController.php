<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Entity\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function statistics() {
        $user = auth()->user();
        $totalTasks = Task::get()->count();
        $allPendingTasks = Task::where("status","pending")->get()->count();
        $myPendingTasks = Task::where("assigned_user_id",$user->id)->where("status","pending")->get()->count();
        $allCompletedTasks = Task::where("status","completed")->get()->count();
        $myCompletedTasks = Task::where("assigned_user_id",$user->id)->where("status","completed")->get()->count();
        $allInProgressTasks = Task::where("status","in_progress")->get()->count();
        $myProgressTasks = Task::where("assigned_user_id",$user->id)->where("status","in_progress")->get()->count();
        $tasks = TaskResource::collection(Task::where("assigned_user_id",$user->id)->limit(8)->get());
        return inertia("Dashboard",compact("user","tasks","totalTasks","allPendingTasks","myPendingTasks","allCompletedTasks","myCompletedTasks","allInProgressTasks","myProgressTasks"));
    }
}
