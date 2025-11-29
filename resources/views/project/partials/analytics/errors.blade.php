<h4>Failed Requests ({{ $errorLogs }})</h4>

@if ($errorLogs == 0)
    <p>No errors.</p>
@else
    @foreach ($apiLogs as $log)
        @if ($log->status_code != 200)
            <div class="alert alert-danger">
                <b>Status:</b> {{ $log->status_code }} <br>
                <b>Response:</b> {{ $log->response_payload }}
            </div>
        @endif
    @endforeach
@endif
