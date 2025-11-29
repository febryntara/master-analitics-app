<div class="w-full p-6 mx-auto space-y-6 bg-white rounded-lg shadow-md">

    {{-- Top High Confidence --}}
    <div>
        <h4 class="mb-3 text-lg font-semibold text-gray-700">Top High Confidence</h4>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($topHigh as $row)
                <div class="p-4 border-l-4 border-green-500 rounded shadow-sm bg-green-50">
                    <p class="font-semibold text-green-700">{{ $row->sentiment }} ({{ $row->confidence_score }})</p>
                    <p class="text-gray-800 break-words">{{ $row->cleaned_text }}</p>
                </div>
            @empty
                <p class="text-gray-400">No high confidence data available.</p>
            @endforelse
        </div>
    </div>

    {{-- Top Low Confidence --}}
    <div>
        <h4 class="mb-3 text-lg font-semibold text-gray-700">Top Low Confidence</h4>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($topLow as $row)
                <div class="p-4 border-l-4 border-yellow-500 rounded shadow-sm bg-yellow-50">
                    <p class="font-semibold text-yellow-700">{{ $row->sentiment }} ({{ $row->confidence_score }})</p>
                    <p class="text-gray-800 break-words">{{ $row->cleaned_text }}</p>
                </div>
            @empty
                <p class="text-gray-400">No low confidence data available.</p>
            @endforelse
        </div>
    </div>

</div>
