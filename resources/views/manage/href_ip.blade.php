@if ($host && $data->active)
    <a href="http://{{ $data->ip_address }}" target="_blank">{{ $data->ip_address }}</a> 
@else
    {{ $data->ip_address }}
@endif