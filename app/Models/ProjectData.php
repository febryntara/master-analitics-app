<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectData extends Model
{
    protected $table = 'app_project_data';

    protected $fillable = [
        'project_id',
        'raw_id',
        'raw_text',
        'status',
        'error_message',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function processed(): HasOne
    {
        return $this->hasOne(ProcessedData::class, 'project_data_id');
    }

    protected static function booted()
    {
        // delete event to cascade delete related processed data
        static::deleting(function ($projectData) {
            $projectData->processed()->get()->each->delete();
        });
    }
}
