<?php

// Realizzato da Cosimo Mandrillo

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Attachment;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'code',
        'funder',
        'start_date',
        'end_date',
        'status',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role','effort')
                    ->withTimestamps();
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class)->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class,'commentable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function progressStatus()
    {
        if ($this->milestones()->count() === 0) return 'not_started';

        $completed = $this->milestones()->where('status','completed')->count();
        $total = $this->milestones()->count();

        if ($completed === $total) return 'completed';
        return 'in_progress';
    }

    public function progressPercentage()
    {
        $total = $this->milestones()->count();
        if ($total === 0) return 0;

        $completed = $this->milestones()->where('status','completed')->count();
        return (int) round(($completed/$total)*100);
    }
}
