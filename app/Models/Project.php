<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'app_projects';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'raw_text_label',
        'raw_id_label',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(ProjectData::class, 'project_id');
    }

    public function taskLogs(): HasMany
    {
        return $this->hasMany(TaskLog::class, 'project_id');
    }
}
