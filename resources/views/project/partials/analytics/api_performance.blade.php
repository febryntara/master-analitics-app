<h4>API Performance</h4>
<p>Average Response Time: {{ number_format($avgApiTime, 2) }} ms</p>

<h4>Requests</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Status</th>
            <th>Duration (ms)</th>
            <th>Time</th>
        </tr>
    </thead>
    @foreach ($apiLogs as $log)
        <tr>
            <td>{{ $log->status_code }}</td>
            <td>{{ $log->duration_ms }}</td>
            <td>{{ $log->created_at }}</td>
        </tr>
    @endforeach
</table>
