<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProjectEnums\ProjectStatus;
use App\Models\User;
use App\Models\Skill;

class Project extends Model {
    use HasFactory;

    protected $primaryKey = 'project_id';
    
    protected $fillable = [
        'project_name',
        'project_description',
        'project_status',
        'project_deadline',
        'project_price',
        'user_id',
        'client_id',
    ];

    protected $casts = [
        'project_status' => ProjectStatus::class,
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'project_id', 'project_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
    }

    public function skills() {
        return $this->belongsToMany(Skill::class, 'project_skills', 'project_id', 'skill_id');
    }

    public function projectlogs() {
        return $this->hasMany(ProjectLog::class, 'project_id', 'project_id');
    }

    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class, 'project_id', 'project_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'project_id', 'project_id');
    }


    // Logic
    public function averageRating()
    {
        return $this->ratings()->avg('rating_value');
    }
}

?>