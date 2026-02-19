<?php

//Realizzato da: Andrea Amodeo

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function projects()
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    public function tasks()
    {
        return $this->morphedByMany(Task::class, 'taggable');
    }
}
