<button class="btn btn-block dropdown-toggle text-white btn-primary @isset($session) @if (!$session) no-hover @endif @endisset" type="button" id="dropdownMenuButton" @isset($session) @if ($session) {{ 'data-toggle=dropdown' }} @endif @else {{ 'data-toggle=dropdown' }} @endisset aria-haspopup="true" aria-expanded="false">
    <span id="btn-station-session">
        @isset($session)
            @if ($session)
                {{ $session->start_at }}
            @else
                None
            @endif
        @else
            Select Session
        @endisset
    </span>
</button>
<div class="dropdown-menu select-data">
    @foreach ($station_session as $row)
        <a class="dropdown-item station-session @isset($session) @if($session->start_at == $row->start_at){{ 'active' }}@endif @endisset" href="javascript:;" session="{{ $row->id }}">{{ $row->start_at }}</a>
    @endforeach
</div>