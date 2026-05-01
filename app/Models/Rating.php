<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'rating_name',
        'rating_value',
        'project_id',
        'user_id',
        'penilai_id',
    ];

    protected $casts = [
        'rating_value' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id', 'user_id');
    }
}

?>