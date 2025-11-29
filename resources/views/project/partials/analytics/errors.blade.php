<div class="w-full p-6 mx-auto space-y-6 bg-white rounded-lg shadow-md">

    {{-- Failed Requests --}}
    <div>
        <h4 class="mb-3 text-lg font-semibold text-gray-700">Failed Requests ({{ $errorLogs }})</h4>

        @if ($errorLogs == 0)
            <p class="text-gray-400">No errors.</p>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($apiLogs as $log)
                    @if ($log->status_code != 200)
                        <div class="p-4 border-l-4 border-red-500 rounded shadow-sm bg-red-50">
                            <p class="font-semibold text-red-700">Status: {{ $log->status_code }}</p>
                            <p class="text-gray-800 break-words"><b>Response:</b> {{ $log->response_payload }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

</div>
