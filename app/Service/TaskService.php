<?php

namespace App\Service;
use App\Entity\Task;

class TaskService {
    public function transform($name,$status,$sortField,$direction,$priority) {
        $query = Task::query();
        if ($name) {
            $query->where("name","like","%". $name ."%");
        }
        if ($status) {
            $query->where("status", $status);
        }
        if ($priority) {
            $query->where("priority", $priority);
        }
        $tasks = $query->orderBy($sortField,$direction)->paginate(10);
        return $tasks;
    }
    public function transformMyTasks($user,$name,$status,$sortField,$direction,$priority) {
        $query = Task::query();
        $query->where("assigned_user_id",$user);
        if ($name) {
            $query->where("name","like","%". $name ."%");
        }
        if ($status) {
            $query->where("status", $status);
        }
        if ($priority) {
            $query->where("priority", $priority);
        }
        $tasks = $query->orderBy($sortField,$direction)->paginate(10);
        return $tasks;
    }
}
