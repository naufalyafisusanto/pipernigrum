<div class="btn-group button-station" id={{ 'action-'.$data->id }} station-id={{ $data->id }}>
    
    <button class="btn dropdown-toggle button-select @if($data->active){!! 'btn-primary' !!}@else{!! 'btn-secondary disabled' !!}@endif d-flex justify-content-between align-items-center" type="button" style="width: 100px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select</button>
    <div class="dropdown-menu select-action">
        <a class="dropdown-item disabled"><span class="badge badge-primary">{{ $data->name }}</span></a>
        <a class="dropdown-item dropdown-toggle @if($data->active && !$data->running && $data->rotation == 0){!! 'text-success' !!}@else{!! 'text-secondary disabled' !!}@endif" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Start</a>
        <div class="dropdown-menu select-action none-hover">
            <a class="dropdown-item @if($data->active && !$data->running && $data->rotation == 0){!! 'text-success button-action' !!}@else{!! 'disabled text-secondary"' !!}@endif" station-action="start_fast">Fast</a>
            <a class="dropdown-item @if($data->active && !$data->running && $data->rotation == 0){!! 'text-success button-action' !!}@else{!! 'disabled text-secondary"' !!}@endif" station-action="start_medium">Medium</a>
            <a class="dropdown-item @if($data->active && !$data->running && $data->rotation == 0){!! 'text-success button-action' !!}@else{!! 'disabled text-secondary"' !!}@endif" station-action="start_slow">Slow</a>
        </div>
        <a class="dropdown-item @if($data->active && $data->running && $data->rotation == 0){!! 'text-danger button-action' !!}@else{!! 'disabled text-secondary d-none"' !!}@endif" station-action="stop">Stop</a>
        <a class="dropdown-item @if($data->active && !$data->running && $data->rotation == 0){!! 'text-warning button-action' !!}@else{!! 'disabled text-secondary"' !!}@endif" station-action="tare">Tare</a>
        @if($data->active && !$data->running && $data->rotation == 0){!! '<div class="dropdown-divider manage"></div>' !!}@endif
        <a class="dropdown-item @if($data->active && !$data->running && $data->rotation != 1){!! 'text-magenta button-action' !!}@else{!! 'disabled text-secondary d-none"' !!}@endif" station-action="insert">Insert</a>
        <a class="dropdown-item @if($data->active && !$data->running && $data->rotation != 0){!! 'text-danger button-action' !!}@else{!! 'disabled text-secondary d-none"' !!}@endif" station-action="brake">Brake</a>
        <a class="dropdown-item @if($data->active && !$data->running && $data->rotation != -1){!! 'text-warning button-action' !!}@else{!! 'disabled text-secondary d-none"' !!}@endif" station-action="eject">Eject</a>
    </div>
    <div class="ml-2">
        <a class="btn btn-icon button-run btn-secondary text-white disabled"><i class="fa-solid fa-play"></i></a>
    </div>
</div>