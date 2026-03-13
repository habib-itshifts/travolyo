<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'file_extension',
        'file_type',
        'file_size',
        'folder_path',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function getUrlAttribute(): string
    {
        return asset('uploads/' . ltrim($this->file_path, '/'));
    }
}
