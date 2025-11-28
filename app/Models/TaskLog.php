<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLog extends Model
{
    protected $table = 'app_task_logs';

    protected $fillable = [
        'project_id',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'status',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $dates = [
        'started_at',
        'finished_at',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
