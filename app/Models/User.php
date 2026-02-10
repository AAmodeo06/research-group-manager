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
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'global_role',
        'group_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // HELPERS PER I RUOLI GLOBALI
    public function isGlobalPi(): bool {
        return $this->global_role === 'pi';
    }

    public function isSenior(): bool {
        return in_array($this->global_role, ['pi', 'manager']);
    }

    // RELAZIONI
    public function projects() {
        return $this->belongsToMany(Project::class)
                    ->withPivot('role', 'effort')
                    ->withTimestamps();
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    // LOGICA DI PROGETTO (Corretta)
    public function roleInProject(Project $project): ?string {
        $member = $this->projects()->where('project_id', $project->id)->first();
        return $member ? $member->pivot->role : null; // Prende il ruolo LOCALE dal pivot
    }

    public function isPiOfProject(Project $project): bool {
        // Un PI globale è PI di ogni progetto, altrimenti controlliamo il pivot
        if ($this->isGlobalPi()) return true;
        
        return $this->projects()
            ->where('project_id', $project->id)
            ->where('project_user.role', 'pi')
            ->exists();
    }

    public function roleLabel(): string {
        return match ($this->global_role) {
            'pi'           => 'Principal Investigator',
            'manager'      => 'Project Manager',
            'researcher'   => 'Researcher',
            'collaborator' => 'Collaborator',
            default        => ucfirst($this->global_role ?? 'User'),
        };
    }
}