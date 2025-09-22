<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    const MEDIA_TYPE_COURSE = 0;
    const MEDIA_TYPE_LESSON = 1;
    const MEDIA_TYPE_RESOURCE = 3;
    const MEDIA_TYPE_NEWS = 4;
    const MEDIA_TYPE_SETTING = 5;

    use HasFactory;
    protected $table = 'medias';
    protected $fillable = [
        'name',
        'alt',
        'src',
        'type',
    ];

    protected $appends = [
        'src_url',
    ];

    public function getSrcUrlAttribute()
    {
        return $this->src
            ? asset('storage/' . ltrim($this->src, '/'))
            : null;
    }

    public function getSourceEncryptFolderUrlAttribute()
    {
        return $this->source_encrypt_folder
            ? asset('storage/' . ltrim($this->source_encrypt_folder, '/'))
            : null;
    }

    public function getSourceEncryptFileUrlAttribute()
    {
        return $this->source_encrypt_file_path
            ? asset('storage/' . ltrim($this->source_encrypt_file_path, '/'))
            : null;
    }

    public function getViSubUrlAttribute()
    {
        return $this->vi_sub
            ? asset('storage/' . ltrim($this->vi_sub, '/'))
            : null;
    }

    public function getEnSubUrlAttribute()
    {
        return $this->en_sub
            ? asset('storage/' . ltrim($this->en_sub, '/'))
            : null;
    }

    public function getAudioUrlAttribute()
    {
        return $this->audio
            ? asset('storage/' . ltrim($this->audio, '/'))
            : null;
    }

    public function getWatermarkUrlAttribute()
    {
        return $this->watermark
            ? asset('storage/' . ltrim($this->watermark, '/'))
            : null;
    }
}
