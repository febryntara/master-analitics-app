<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'app_api_logs';

    protected $fillable = [
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'duration_ms',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
