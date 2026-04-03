<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model {
    use HasFactory;

    protected $primaryKey = 'skill_id';
    protected $fillable = [
        'skill_name',
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_skills', 'skill_id', 'user_id');
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'project_skills', 'skill_id', 'project_id');
    }
}

?>