<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements CanResetPasswordContract, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'global_role',
        'group_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
                    ->withPivot('role', 'effort')
                    ->withTimestamps();
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }

    public function authoredPublications()
    {
        return $this->hasMany(Author::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roleInProject(Project $project): ?string
    {
        return $this->projects()
            ->where('project_id', $project->id)
            ->first()
            ?->pivot
            ?->role;
    }

    public function isPiOfProject(Project $project): bool
    {
        return $this->projects()
        ->where('project_id', $project->id)
        ->where('role', 'pi')
        ->exists();
    }
}