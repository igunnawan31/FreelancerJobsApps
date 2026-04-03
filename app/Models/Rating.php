<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Rating extends Model {
    use HasFactory;

    protected $primaryKey = 'rating_id';
    protected $fillable = [
        'rating_name',
        'rating_value',
        'user_id',
        'penilai_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function penilai() {
        return $this->belongsTo(User::class, 'penilai_id', 'user_id');
    }
}

?>