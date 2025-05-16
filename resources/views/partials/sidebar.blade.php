<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mb-3" style="height: 150px;">
            <a href="javascript:;">
                <span class="d-flex flex-column">
                    <div class="mt-4"><img src="/assets/img/pipernigrum.png" alt="Piper Nigrum Logo" height="100px"></div>
                    <div class="mt-1" style="height: 20px;"><h6 style="color: #404040;">Piper Nigrum</h6></div>
                </span>
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="javascript:;"><img src="/assets/img/pipernigrum.png" alt="Piper Nigrum Logo" height="25px"></a>
        </div>
        <ul class="sidebar-menu">
            <li class=@if($page == 'dashboard'){{ 'active' }}@endif><a class="nav-link" href="/dashboard"><i class="fas fa-desktop"></i><span class="mt-0">Dashboard</span></a></li>
            <li class="nav-item dropdown @if ($page == 'station') active clicked @endif">
                <a class="nav-link has-dropdown"><i class="fas fa-wifi"></i><span class="mt-0">Station</span></a>
                <ul class="dropdown-menu">
                    @empty($stations)
                        @php
                            $stations = stations();
                        @endphp
                    @endempty
                    @forelse ($stations as $row_station)
                        <li class=@isset($station)@if($row_station->id == $station->id && $page == 'station'){{ 'active' }}@endif @endisset>
                            <a class="nav-link hover" href={{ route('station.index').'?id='.$row_station->id }}>
                                @if ($row_station->active)
                                    <i class="fas fa-circle ml-0 mr-1 @if($row_station->running){{ 'text-success' }}@else{{ 'text-danger' }}@endif" style="scale: 1.25"></i>
                                @else
                                    <i class="fas fa-wifi-slash ml-0 mr-1"></i> 
                                @endif
                                {{ $row_station->name }} 
                            </a>
                        </li>
                    @empty
                        <li>
                            <a class="nav-link disabled">No station available</a>
                        </li>
                    @endforelse
                </ul>
            </li>
            @can('admin')
                <li class=@if($page == 'scan'){{ 'active' }}@endif><a class="nav-link" href="/scan"><i class="fas fa-qrcode"></i><span class="mt-0">Scan</span></a></li>
            @endcan
            <li class=@if(strpos($page, 'manage') !== false){{ 'active' }}@endif><a class="nav-link" href="/manage"><i class="fas fa-sliders"></i><span class="mt-0">Manage</span></a></li>
            @can('admin')
                <li class=@if($page == 'download'){{ 'active' }}@endif><a class="nav-link" href="/download"><i class="fas fa-download"></i><span class="mt-0">Download</span></a></li>
                <li class=@if($page == 'users'){{ 'active' }}@endif><a class="nav-link" href="/users"><i class="fas fa-users"></i><span class="mt-0">Users</span></a></li>
            @endcan
            <li class="@if($page == 'logs'){{ 'active' }}@endif pb-5"><a class="nav-link" href="/logs"><i class="fas fa-clock-rotate-left"></i><span class="mt-0">Logs</span></a></li>
        </ul>
    </aside>
</div>