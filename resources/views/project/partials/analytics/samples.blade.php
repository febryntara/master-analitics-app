<h4>Top High Confidence</h4>
@foreach ($topHigh as $row)
    <div class="p-2 mb-2 card">
        <b>{{ $row->sentiment }} ({{ $row->confidence_score }})</b><br>
        {{ $row->cleaned_text }}
    </div>
@endforeach

<h4>Top Low Confidence</h4>
@foreach ($topLow as $row)
    <div class="p-2 mb-2 card">
        <b>{{ $row->sentiment }} ({{ $row->confidence_score }})</b><br>
        {{ $row->cleaned_text }}
    </div>
@endforeach
