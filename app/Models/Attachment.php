<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Attachment extends Model
{
    protected $fillable = ['file_path', 'original_name', 'mime_type'];
    use HasFactory;
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
