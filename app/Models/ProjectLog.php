<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'actor_id',
        'action',
        'comment',
        'revision_number',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function attachments()
{
    return $this->hasMany(ProjectAttachment::class, 'project_log_id', 'id');
}
}
