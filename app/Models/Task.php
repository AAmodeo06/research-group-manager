<?php

// Realizzato da Andrea Amodeo

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_id',
        'milestone_id',
        'assignee_id',
        'title',
        'description',
        'due_date',
        'status',
        'priority'
    ];

    protected $casts =[
        'due_date' => 'datetime',
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
        && Carbon::parse($this->due_date)->isPast()
        && $this->status !== 'completed';
    }
}
