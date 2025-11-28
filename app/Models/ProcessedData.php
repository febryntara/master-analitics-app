<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessedData extends Model
{
    protected $table = 'app_processed_data';

    protected $fillable = [
        'project_data_id',
        'cleaned_text',
        'sentiment',
        'confidence_score',
        'preprocessing_time_ms',
        'model_version',
    ];

    public function projectData(): BelongsTo
    {
        return $this->belongsTo(ProjectData::class);
    }
}
