<?php

namespace App\Entity;

use App\Entity\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = ["name","status","description","image_path","due_date" , "created_by", "updated_by"];

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class,"created_by");
    }

    public function updatedBy() {
        return $this->belongsTo(User::class,"updated_by");
    }
}
