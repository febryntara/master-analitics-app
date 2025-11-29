<div class="w-full p-6 mx-auto space-y-6 bg-white rounded-lg shadow-md">

    {{-- API Performance --}}
    <div>
        <h4 class="mb-2 text-lg font-semibold text-gray-700">API Performance</h4>
        <p class="font-medium text-gray-900">Average Response Time: {{ number_format($avgApiTime, 2) }} ms</p>
    </div>

    {{-- Requests Tables Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        @php
            $half = ceil($apiLogs->count() / 2);
            $firstHalf = $apiLogs->slice(0, $half);
            $secondHalf = $apiLogs->slice($half);
        @endphp

        {{-- Table 1 --}}
        <div>
            <h4 class="mb-3 text-lg font-semibold text-gray-700">Requests (Part 1)</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Status</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Duration (ms)</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($firstHalf as $log)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ $log->status_code }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $log->duration_ms }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-400">No requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Table 2 --}}
        <div>
            <h4 class="mb-3 text-lg font-semibold text-gray-700">Requests (Part 2)</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Status</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Duration (ms)</th>
                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($secondHalf as $log)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ $log->status_code }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $log->duration_ms }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-400">No requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $apiLogs->links() }}
    </div>

</div>
