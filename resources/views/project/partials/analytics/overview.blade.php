<div class="max-w-sm p-6 space-y-6 bg-white rounded-lg shadow-md">

    {{-- Sentiment Distribution --}}
    <div>
        <h4 class="mb-3 text-lg font-semibold text-gray-700">Sentiment Distribution</h4>
        @if ($sentimentPercent)
            <div class="space-y-2">
                <div class="flex items-center justify-between px-4 py-2 text-green-700 rounded bg-green-50">
                    <span>Positive</span>
                    <span>{{ $sentimentPercent['positive'] }}%</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2 text-red-700 rounded bg-red-50">
                    <span>Negative</span>
                    <span>{{ $sentimentPercent['negative'] }}%</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2 text-gray-700 bg-gray-100 rounded">
                    <span>Neutral</span>
                    <span>{{ $sentimentPercent['neutral'] }}%</span>
                </div>
            </div>
        @else
            <p class="text-gray-400">No sentiment data available.</p>
        @endif
    </div>

    {{-- Average Confidence --}}
    <div>
        <h4 class="mb-2 text-lg font-semibold text-gray-700">Average Confidence</h4>
        <p class="text-xl font-medium text-gray-900">{{ number_format($avgConfidence, 3) }}</p>
    </div>

</div>
