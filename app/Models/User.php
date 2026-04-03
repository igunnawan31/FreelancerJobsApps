<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\UserEnums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Rating;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'profile_picture', // Picture User
        'portfolio', // Portfolio User (Can be Link or File)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    public function skills() {
        // skills <--> users (Many to Many)
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    public function projects() {
        // user --> projects (One to Many)
        return $this->hasMany(Project::class, 'user_id', 'user_id');
    }

    public function totalProjects() {
        // counting all the projects from user
        return $this->projects()->count();
    }

    public function canTakeProject() {
        // checking if user has less than 3 running projects
        return $this->projects()
            ->where('project_status', ProjectStatus::STATUS_RUNNING)
            ->count() < 3;
    }

    public function ratings() {
        // user --> ratings (One to Many)
        return $this->hasMany(Rating::class, 'user_id', 'user_id');
    }

    public function averageRating() {
        // calculate average rating from user
        return $this->ratings()->avg('rating_value');
    }

    public function totalRatings() {
        // counting all the ratings from user
        return $this->ratings()->count();
    }
}
