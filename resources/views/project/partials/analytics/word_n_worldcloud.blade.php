<h4>Top Words</h4>
<ol>
    @foreach ($words as $w => $c)
        <li>{{ $w }} ({{ $c }})</li>
    @endforeach
</ol>

<h4>Wordcloud</h4>
@if ($wordcloudUrl)
    <img src="{{ $wordcloudUrl }}" class="img-fluid">
@else
    <p>No wordcloud available.</p>
@endif
