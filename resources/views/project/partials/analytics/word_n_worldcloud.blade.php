<h4 class="mb-3 text-lg font-semibold text-gray-700">Top Words</h4>
<div class="grid grid-cols-1 gap-2 lg:grid-cols-3">
    {{-- Top Words --}}
    @foreach ($words as $chunk)
        <div class="max-w-sm p-6 space-y-6 bg-white rounded-lg shadow-md">
            @if ($words && count($words))
                <ol class="space-y-1 text-gray-800 list-decimal list-inside">
                    @foreach ($chunk as $w => $c)
                        <li class="flex justify-between">
                            <span>{{ $w }}</span>
                            <span class="font-medium text-gray-900">{{ $c }}</span>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="text-gray-400">No words available.</p>
            @endif
        </div>
    @endforeach


    {{-- Wordcloud --}}
    <div class="mt-3">
        <h4 class="mb-3 text-lg font-semibold text-gray-700">Wordcloud</h4>
        @if ($wordcloudUrl)
            <img src="{{ $wordcloudUrl }}" class="w-full rounded-lg shadow-sm" alt="Wordcloud">
        @else
            <p class="text-gray-400">No wordcloud available.</p>
        @endif
    </div>

</div>
