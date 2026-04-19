<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'project_id',
        'project_log_id',
        'payment_method',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'note'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function log()
    {
        return $this->belongsTo(ProjectLog::class, 'project_log_id', 'id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
    }
}
