<?php

//Realizzato da: Cosimo Mandrillo

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'path',
        'title',
        'type',
        'version',
        'uploaded_by'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function previousVersions()
    {
        return self::where('attachable_type', $this->attachable_type)
            ->where('attachable_id', $this->attachable_id)
            ->where('type', $this->type)
            ->where('version', '<', $this->version)
            ->orderByDesc('version');
    }

    public const TYPE_PROJECT_FILE = 'project_file';
    public const TYPE_PUBLICATION_PDF = 'publication_pdf';
    public const TYPE_SUPPLEMENTARY = 'supplementary';

}
