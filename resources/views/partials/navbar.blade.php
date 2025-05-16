<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <ul class="navbar-nav">
        <li><a href="javascript:;" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
    <a class="navbar-brand mx-auto interval-seconds datetime text-white" id="datetime">0000-00-00 00:00:00</a>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown" style="padding-top:5px;">
            @php
                $avatar = Avatar::create(auth()->user()->name);
            @endphp
            <a href="javascript:;" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img src="{{ $avatar->toBase64() }}" style="width:35px !important;height:35px !important; padding-bottom:3px;"/>
                <div class="d-sm-none d-lg-inline-block mr-1 ml-1">{{ auth()->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-3 mt-2" style="max-width: 170px">
                <div class="dropdown-title py-1 d-lg-none" style="margin-left: 6px; color:{{ $avatar->background }} !important;">{{ auth()->user()->name }}</div>
                <div class="dropdown-title pt-1" style="margin-left: 6px;"><span class="badge text-white @if(auth()->user()->admin){{ 'badge-info' }}@else{{ 'badge-success' }}@endif" style="font-size: 10px;">{{ auth()->user()->role() }}</span></div>
                <div class="dropdown-divider d-lg-none"></div>
                <a href={{ route('me.settings') }} class="dropdown-item has-icon">
                    <i class="far fa-cog" style="margin-top: 1px"></i> User Settings
                </a>
                <a href={{ route('me.cert', ['filename' => 'pipernigrum.local.crt']) }} class="dropdown-item has-icon">
                    <i class="far fa-file-certificate" style="margin-top: 1px"></i> SSL Certificate
                </a>
                {{-- @can('admin')
                    <a href={{ route('me.logs', ['filename' => 'login.log']) }} class="dropdown-item has-icon">
                        <i class="far fa-rectangle-history-circle-user style="margin-top: 1px"></i> Log Login
                    </a>
                @endcan --}}
                <div class="dropdown-divider"></div>
                <form action={{ route('auth.logout') }} method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item has-icon text-danger" style="font-size:13px; padding: 10px 20px; padding-left: 24px; font-weight: 500; line-height: 1.2; border: none; outline: none;">
                        <i class="fas fa-sign-out-alt" style="margin-top: 2px"></i> Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>