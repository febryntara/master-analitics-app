<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectData;
use App\Models\ProcessedData;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function show(Project $project)
    {
        // ambil semua processed data
        $processed = ProcessedData::whereIn(
            'project_data_id',
            ProjectData::where('project_id', $project->id)->pluck('id')
        )->get();

        // SENTIMENT METRIC
        $sentimentCount = [
            'positive' => $processed->where('sentiment', 'positif')->count(),
            'negative' => $processed->where('sentiment', 'negatif')->count(),
            'neutral'  => $processed->where('sentiment', 'netral')->count(),
        ];

        $total = $processed->count();
        $sentimentPercent = $total > 0 ? [
            'positive' => round($sentimentCount['positive'] / $total * 100, 2),
            'negative' => round($sentimentCount['negative'] / $total * 100, 2),
            'neutral'  => round($sentimentCount['neutral'] / $total * 100, 2),
        ] : null;

        // AVERAGE CONFIDENCE
        $avgConfidence = $processed->avg('confidence_score');

        // TOP WORDS
        $allText = $processed->pluck('cleaned_text')->implode(' ');
        $words = collect(explode(' ', $allText))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(30)->chunk(10);

        // WORDCLOUD - ambil dari Flask
        $wordcloudUrl = null;
        if ($words->count() > 0) {
            $res = Http::post(config('services.flask.url') . '/wordcloud', [
                'tokens' => $words->keys()->toArray()
            ]);

            if ($res->ok()) {
                $wordcloudUrl = "data:image/png;base64," . base64_encode($res->body());
            }
        }

        // API PERFORMANCE
        $apiLogs = ApiLog::where('endpoint', '/preprocess')
            ->where('project_id', $project->id)
            ->orderBy('created_at')
            ->paginate(50);

        $avgApiTime = $apiLogs->avg('duration_ms');
        $errorLogs = $apiLogs->where('status_code', '!=', 200)->count();

        // SAMPLES
        $topHigh = $processed->sortByDesc('confidence_score')->take(5);
        $topLow = $processed->sortBy('confidence_score')->take(5);

        return view('project.analytics', [
            'project' => $project,
            'sentimentPercent' => $sentimentPercent,
            'avgConfidence' => $avgConfidence,
            'words' => $words,
            'wordcloudUrl' => $wordcloudUrl,
            'apiLogs' => $apiLogs,
            'avgApiTime' => $avgApiTime,
            'errorLogs' => $errorLogs,
            'topHigh' => $topHigh,
            'topLow' => $topLow,
        ]);
    }
}
