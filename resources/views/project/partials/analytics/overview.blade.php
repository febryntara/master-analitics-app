<h4>Sentiment Distribution</h4>

@if ($sentimentPercent)
    <ul>
        <li>Positive: {{ $sentimentPercent['positive'] }}%</li>
        <li>Negative: {{ $sentimentPercent['negative'] }}%</li>
        <li>Neutral: {{ $sentimentPercent['neutral'] }}%</li>
    </ul>
@endif

<h4>Average Confidence Score</h4>
<p>{{ number_format($avgConfidence, 3) }}</p>
