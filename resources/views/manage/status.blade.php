@if ($data->active)
    @if ($data->running)
        <div class="badge priority-a badge-success">Running <span class="interval-seconds time">@isset($running){{ $running[$data->id] }}@else{{ '00:00:00' }}@endisset</span></div>
    @else
        <div class="badge priority-b badge-danger">Stopped</div>
        @if ($data->rotation === 1)
            <div class="badge priority-a badge-magenta">Insert <i class="fa-solid fa-rotate-right rotate-cw"></i></div>
        @elseif ($data->rotation === -1)
            <div class="badge priority-a badge-warning">Eject <i class="fa-solid fa-rotate-left rotate-ccw"></i></div>
        @elseif ($data->rotation === 0)
            <div class="badge priority-b badge-danger">Brake</div>
        @endif
    @endif
@else
    <div class="badge priority-c badge-grey text-white">Disconnected</div>
@endif