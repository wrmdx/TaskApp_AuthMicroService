<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    protected $fillable = ["name","description","image_path","due_date" , "created_by", "updated_by","assigned_user_id","status","priority","project_id"];
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class,"created_by");
    }

    public function updatedBy() {
        return $this->belongsTo(User::class,"updated_by");
    }

    public function assignedUser() {
        return $this->belongsTo(User::class,"assigned_user_id");
    }
}
