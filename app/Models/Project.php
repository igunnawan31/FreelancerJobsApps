<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\ProjectEnums\ProjectType;
use App\Models\User;
use App\Models\Skill;

class Project extends Model {
    use HasFactory;

    protected $primaryKey = 'project_id';
    
    protected $fillable = [
        'project_name',
        'project_description',
        'project_type',
        'project_status',
        'project_attachment',
        'project_deadline',
        'user_id',
    ];

    protected $casts = [
        'project_status' => ProjectStatus::class,
        'project_type' => ProjectType::class,
        'project_attachment' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function skills() {
        return $this->belongsToMany(Skill::class, 'project_skills', 'project_id', 'skill_id');
    }

    public function countAttachments() {
        return is_array($this->project_attachment)
            ? count($this->project_attachment)
            : 0;
    }
}

?>