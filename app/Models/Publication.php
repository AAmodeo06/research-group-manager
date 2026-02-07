<?php

// Realizzato da Luigi La Gioia

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publication extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'type',
        'venue',
        'doi',
        'status',
        'target_deadline',
        'pdf_path',
    ];
    
    public function authors()
    {
        return $this->hasMany(Author::class)->orderBy('position');
    }
    
    public function projects()
    {
        return $this->belongsToMany(Project::class)
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'authors')
                    ->withPivot('position', 'is_corresponding')
                    ->orderBy('authors.position');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function isDraft(): bool
    {
        return $this->status === 'drafting';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'drafting' => 'Drafting',
            'submitted' => 'Submitted',
            'accepted' => 'Accepted',
            'published' => 'Published',
            default => ucfirst($this->status),
        };
    }
}
