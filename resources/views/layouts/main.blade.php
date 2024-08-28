@extends('layouts.initial')

@section('title')
    <title>
        @if ($page == 'station')
            {{ $station->name }}
        @elseif ($page == 'manage_params')
            {{ 'Params '.$station->name }}
        @elseif ($page == 'manage_station')
            {{ 'Config '.$station->name }}
        @else
            {{ ucfirst($page) }}
        @endif
        - Piper Nigrum
    </title>
@endsection

@push('style')
    <style>
        .datetime {
            font-size: 1.4rem;
        }

        @media only screen and (max-width: 767px) {
            .datetime {
                font-size: 1rem;
            }
        }

        .header-center {
            justify-content: center;
            border-bottom-color: transparent !important; 
            padding-top: 20px !important;
            padding-bottom: 0px !important;
            min-height: 0px !important;
        }
    </style>
@endpush

@section('body')
    <div id="app">
        <div class="main-wrapper">
            
            @include('partials.navbar')
            
            @include('partials.sidebar')

            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1><span class="color-pipernigrum">
                            @if ($page == 'manage_params')
                                {{ 'Parameters '.$station->name }}
                            @elseif ($page == 'manage_station')
                                {{ 'Configuration '.$station->name }}
                            @elseif ($page != 'station')
                                {{ ucfirst($page) }}
                            @endif
                        </span></h1>
                        @yield('h1')
                    </div>

                    @yield('main-body')
                </section>
            </div>
            <footer class="main-footer d-flex" style="justify-content: center; background-color: #fff;">
                <div class="footer-left text-center">
                    Copyright &copy; 2024 <div class="bullet d-none d-sm-inline"> </div><br class="d-inline d-sm-none"><a href=""> Piper Nigrum - Universitas Diponegoro</a>
                </div>
            </footer>
        </div>
    </div>
@endsection
    
@push('script')
    @if (session()->has('loginSuccess'))
        <script>
            $(document).ready(function() {
                iziToast.success({
                    title: 'Login Success',
                    message: 'You are now logged in as {{ session()->get('loginSuccess') }}.',
                    timeout: 5000,
                    overlayColor: 'rgba(0, 0, 0, 1.0)',
                    position: 'topRight'
                });
            });
        </script>
    @endif

    <script>
        function padNumber(number) {
            return (number < 10 ? '0' : '') + number;
        }

        function formatDuration(totalSeconds) {
            return padNumber(Math.floor(totalSeconds / 3600)) + ':' + padNumber(Math.floor((totalSeconds % 3600) / 60)) + ':' + padNumber(totalSeconds % 60);
        }

        function handleInterval(element) {
            if ($(element).hasClass("datetime")) {
                var serverTime = new Date($(element).html());
                serverTime.setSeconds(serverTime.getSeconds() + 1);
                const resultTime = serverTime.getFullYear() + '-' + padNumber(serverTime.getMonth() + 1) + '-' + padNumber(serverTime.getDate()) + ' ' + padNumber(serverTime.getHours()) + ':' + padNumber(serverTime.getMinutes()) + ':' + padNumber(serverTime.getSeconds());
                if (!isNaN(serverTime)) $(element).html(resultTime);
            } else if ($(element).hasClass("time") && !$(element).hasClass("freeze")) {
                const [hours, minutes, seconds] = $(element).html().split(':').map(Number);
                const totalSeconds = hours * 3600 + minutes * 60 + seconds + 1;
                const resultTime = formatDuration(totalSeconds);
                if (!isNaN(totalSeconds) && totalSeconds >= 0) $(element).html(resultTime);
            }
        }

        $(document).ready(function() {
            $.ajax({
                url: '{{ route('datetime') }}',
                type: 'GET',
                success: function(data) {
                    $("#datetime").html(data);
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });

            setInterval(function() {
                $('.interval-seconds').each(function() {
                    handleInterval(this);
                });
            }, 1000);
        });
    </script>
@endpush