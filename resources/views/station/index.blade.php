@extends('layouts.main')

@push('style')
    <style>
        .icon-header {
            transform: scale(1.5);
            padding-top: 6px;
            margin-right: 23px;
        }

        .h1-header {
            margin-right: 20px;
        }

        .datetime-header {
            padding-top: 7px;
        }

        @media only screen and (max-width: 570px) {
            .icon-header {
                transform: scale(1.2);
                margin-right: 15px;
            }

            .h1-header {
                margin-right: 15px;
            }

            .datetime-header {
                padding-top: 4px;
            }
        }

        .chartWrapper {
            position: relative;
            
        }

        .chartWrapper > canvas {
            position: absolute;
            left: 0;
            top: 0;
            pointer-events:none;
        }

        .chartAreaWrapper {
            position: relative;
            width: 100%;
        }

        .chartAreaWrapper2 {
            position: relative;
            height: 300px;
        }
    </style>
@endpush

@section('h1')
    <h1><span class="color-pipernigrum h1-header">{{ $station->name }}</span></h1>
    <span id="status">
        @if ($station->active)
            <i class="fas fa-circle icon-header @if ($station->running) text-success @else text-danger @endif "></i>
        @else
            <i class="fas fa-wifi-slash icon-header"></i>
        @endif
    </span>
    <small class="datetime-header" style="font-size: 100%;">Update <span id="last_update">none</span></small>
@endsection

@section('main-body')
    <div class="section-body">
        <div class="row">
            <a id="here-modal" class="btn btn-primary text-white" hidden></a>
            <div class="col-md-12 col-lg-6 col-12 custom-column-select">
                <div class="card" style="height: 100px">
                    <div class="card-body pt-3 mb-2" style="height: 20%">
                        <h5 class="text-center color-pipernigrum">Select Session</h5>
                    </div>
                    <div class="card-body p-2" style="margin-top: 3px">
                        <div class="row">
                            <div class="col pr-1" style="padding-left: 17px">
                                <div class="dropdown d-inline mr-2 session-date">
                                    <button class="btn btn-block dropdown-toggle text-white btn-primary @isset($session) @if (!$session) no-hover @endif @endisset" type="button" id="dropdownMenuButton" @if ($session) {{ 'data-toggle=dropdown' }} @endif aria-haspopup="true" aria-expanded="false">
                                        <span id="btn-session-date">
                                            @if ($session)
                                                {{ $session->date }}
                                            @else
                                                {{ 'None' }}
                                            @endif
                                        </span>
                                    </button>
                                    <div class="dropdown-menu select-data">
                                        @foreach ($station_date as $row)
                                            <a class="dropdown-item station-date @isset($session) @if($session->date == $row->date){{ 'active' }}@endif @endisset" href="javascript:;" date="{{ $row->date }}">{{ $row->date }} ({{ $row->count }})</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1" style="padding-right: 17px">
                                <div class="dropdown d-inline mr-2 session-list" id="session-list">
                                    @include('station.session')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #8d6753">
                        <i class="far fa-weight-hanging" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4><span id="mass-title">Mass</span></h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="mass-value" class="color-pipernigrum">0<small> g</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #5f5f5f">
                        <i class="far fa-stopwatch" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Duration</h4>
                        </div>
                        <div class="card-body p-1">
                            <h4 id="duration-value" class="color-pipernigrum interval-seconds time freeze" style="margin-bottom: 0px; font-size: 1.4rem;">00:00:00</h4>
                            <h6 id="duration-status" style="font-size: 14px;">NONE</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #ffdb25">
                        <i class="fas fa-v" style="transform: scale(1.4);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Voltage Source</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="voltage-value" class="color-pipernigrum">0<small> Volt</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #00fff7">
                        <i class="fas fa-i" style="transform: scale(1.4);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Current Source</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="current-value" class="color-pipernigrum">0<small> A</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #507ffe">
                        <i class="fas fa-wave-sine" style="transform: scale(1.3);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Frequency Source</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="frequency-value" class="color-pipernigrum">0<small> Hz</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #e95cff">
                        <i class="fas fa-lambda" style="transform: scale(1.4);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Power Factor Source</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="power-factor-value" class="color-pipernigrum">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #ff8f0f">
                        <i class="far fa-temperature-three-quarters" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Dryer Temperature</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="temp-value" class="color-pipernigrum">0<small> &deg;C</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #68bcf3">
                        <i class="far fa-droplet" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Relative Humidity</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="humidity-value" class="color-pipernigrum">0<small> %</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #ff552b">
                        <i class="far fa-plug" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Power Used</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="power-value" class="color-pipernigrum">0<small> Watt</small></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 custom-column">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background-color: #00cc66">
                        <i class="far fa-bolt" style="transform: scale(1.5);"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Energy Used</h4>
                        </div>
                        <div class="card-body p-1">
                            <h3 id="energy-value" class="color-pipernigrum">0<small> Wh</small></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h6 class="text-center color-pipernigrum">Voltage Source Graph</h6>
                    </div>
                    <div class="card-body p-3 chartWrapper">
                        <div class="chartAreaWrapper">
                            <div class="chartAreaWrapper2">
                                <canvas id="chartVolt" height="300px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h6 class="text-center color-pipernigrum">Power Used Graph</h6>
                    </div>
                    <div class="card-body p-3 chartWrapper">
                        <div class="chartAreaWrapper">
                            <div class="chartAreaWrapper2">
                                <canvas id="chartPower" height="300px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h6 class="text-center color-pipernigrum">Dryer Temperature Graph</h6>
                    </div>
                    <div class="card-body p-3 chartWrapper">
                        <div class="chartAreaWrapper">
                            <div class="chartAreaWrapper2">
                                {{-- <canvas id="chartTemp" height="300px"></canvas> // Mods --}}
                                <canvas id="chartTemp" height="700px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h6 class="text-center color-pipernigrum">Relative Humidity Graph</h6>
                    </div>
                    <div class="card-body p-3 chartWrapper">
                        <div class="chartAreaWrapper">
                            <div class="chartAreaWrapper2">
                                <canvas id="chartHumidity" height="300px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>              
    </div>
@endsection

@push('script')
    <script src="/assets/js/station/chart.js"></script>
    <script src="/assets/js/station/luxon.js"></script>
    <script src="/assets/js/station/chartjs-adapter-luxon.js"></script>

    <script>
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        if (screenWidth < 500) {
            $('.chartAreaWrapper').css('overflow-x', 'scroll');
            $('.chartAreaWrapper2').css('width', '600px');
        }

        var chartTemp, chartPower; 
        var intervalPage;
        var id_session = @if($session){{ $session->id }}@else{{ '0' }}@endif;
        
        luxon.Settings.defaultLocale = "id";
    
        function drawChart(id, data, label, xkey, ykey, brColor, bgColor, xtitle, ytitle) {
            var chart = new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    datasets: [{
                        label: label,
                        data: data,
                        parsing: {
                            xAxisKey: xkey,
                            yAxisKey: ykey
                        },
                        borderWidth: 1,
                        borderColor: brColor,
                        backgroundColor: bgColor,
                        // pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: xtitle
                            },
                            type: 'time',
                            ticks: {
                                major: {
                                    enabled: true
                                },
                                font: (context) => {
                                    const boldedTicks = context.tick && context.tick.major ? 'bold' : '';
                                    return {weight: boldedTicks}
                                }
                            },
                            time: {
                                unit: 'second',
                                displayFormats: {
                                    minute: 'HH:mm',
                                    second: 'ss'
                                }
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: ytitle
                            }
                        }
                    }
                }
            });
            return chart;
        }
    
        function station_date(date, element) {
            $.ajax({
                url: '{{ route('station.date').'?id='.$station->id }}&date=' + date,
                type: 'GET',
                async: true,
                success: function(response) {
                    $("#btn-session-date").html(date);
                    $(element).closest('.select-data').find('.station-date').removeClass('active');
                    $(element).addClass('active');
                    $("#session-list").html(response);
                    $(".station-session").on("click", function() {
                        station_session(this);
                    });
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }
        
        function station_session(element) {
            id_session = $(element).attr('session');
            $(element).closest('.select-data').find('.station-session').removeClass('active');
            $(element).addClass('active');
            $("#btn-station-session").html($(element).html());

            $("#last_update").html('0000-00-00 00:00:00');
            $("#mass-value").html('0<small> g</small>');
            $("#duration-value").html(formatDuration(0)).addClass('freeze');
            $("#voltage-value").html('0<small> Volt</small>');
            $("#current-value").html('0<small> A</small>');
            $("#frequency-value").html('0<small> Hz</small>');
            $("#power-factor-value").html('0');
            $("#temp-value").html('0<small> &deg;C</small>');
            $("#humidity-value").html('0<small> %</small>');
            $("#power-value").html('0<small> Watt</small>');
            $("#energy-value").html('0<small> Wh</small>');
            
            chartPower.data.datasets[0].data = [];
            chartVolt.data.datasets[0].data = [];
            chartTemp.data.datasets[0].data = [];
            chartHumidity.data.datasets[0].data = [];

            chartVolt.update();
            chartPower.update();
            chartTemp.update();
            chartHumidity.update();

            intervalPage = clearInterval(intervalPage);
            loadPage();
        }
    
        function loadPage() {
            $.ajax({
                url: '{{ route('station.load').'?id_session=' }}' + id_session,
                type: 'GET',
                async: true,
                success: function(response) {
                    let dataChart = response.data.chart.map(item => {
                        return {
                            timestamp: new Date(item.timestamp),
                            voltage: item.voltage,
                            power: item.power,
                            temp: item.temp,
                            humidity: item.humidity,
                            id: item.id
                        };
                    });

                    if ((response.data.card.status != "FINISHED") && (response.data.card.status != "STOPPED")) {
                        $("#duration-value").removeClass('freeze');
                        intervalPage = setInterval(updatePage, 10000);
                    }
    
                    $("#last_update").html(response.data.last_update);
                    $("#mass-title").html(response.data.card.mass_title);
                    $("#mass-value").html(response.data.card.mass + '<small> g</small>');
                    $("#duration-value").html(response.data.card.duration);
                    $("#duration-status").html(response.data.card.status);
                    $("#voltage-value").html(response.data.card.voltage.toFixed(0) + '<small> Volt</small>');
                    $("#current-value").html(response.data.card.current.toFixed(2) + '<small> A</small>');
                    $("#frequency-value").html(response.data.card.frequency + '<small> Hz</small>');
                    $("#power-factor-value").html(response.data.card.power_factor.toFixed(2));
                    $("#temp-value").html(response.data.card.temp.toFixed(1) + '<small> &deg;C</small>');
                    $("#humidity-value").html(response.data.card.humidity.toFixed(1) + '<small> %</small>');
                    $("#power-value").html(response.data.card.power.toFixed(0) + '<small> Watt</small>');
                    $("#energy-value").html(response.data.card.energy.toFixed(1) + '<small> Wh</small>');
                    
                    chartPower.data.datasets[0].data = dataChart;
                    chartVolt.data.datasets[0].data = dataChart;
                    chartTemp.data.datasets[0].data = dataChart;
                    chartHumidity.data.datasets[0].data = dataChart;

                    chartVolt.update();
                    chartPower.update();
                    chartTemp.update();
                    chartHumidity.update();
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }

        function updatePage() {
            $.ajax({
                url: '{{ route('station.update').'?id_session=' }}' + id_session + '&last_id=' + chartTemp.data.datasets[0].data[0]['id'],
                type: 'GET',
                async: true,
                success: function(response) { 
                    if ((response.data.card.status == "FINISHED") || (response.data.card.status == "STOPPED")) {
                        $('#status').html('<i class="fas fa-circle icon-header text-danger" style="margin-right: 23px;"></i>');
                        $("#duration-value").addClass('freeze');
                        $("#duration-status").html(response.data.card.status);
                        intervalPage = clearInterval(intervalPage);
                    } else {
                        let updateChart = response.data.chart.map(item => {
                            return {
                                timestamp: new Date(item.timestamp),
                                voltage: item.voltage,
                                power: item.power,
                                temp: item.temp,
                                humidity: item.humidity,
                                id: item.id
                            };
                        });

                        if (response.data.last_update != 0) $("#last_update").html(response.data.last_update);
                        $("#mass-value").html(response.data.card.mass + '<small> g</small>');
                        $("#duration-value").html(response.data.card.duration);
                        $("#duration-status").html(response.data.card.status);
                        $("#voltage-value").html(response.data.card.voltage.toFixed(0) + '<small> Volt</small>');
                        $("#current-value").html(response.data.card.current.toFixed(2) + '<small> A</small>');
                        $("#frequency-value").html(response.data.card.frequency + '<small> Hz</small>');
                        $("#power-factor-value").html(response.data.card.power_factor.toFixed(2));
                        $("#temp-value").html(response.data.card.temp.toFixed(1) + '<small> &deg;C</small>');
                        $("#humidity-value").html(response.data.card.humidity.toFixed(1) + '<small> %</small>');
                        $("#power-value").html(response.data.card.power.toFixed(0) + '<small> Watt</small>');
                        $("#energy-value").html(response.data.card.energy.toFixed(1) + '<small> Wh</small>');

                        updateChart.reverse().forEach(function(datum, index) {
                            setTimeout(() => {    
                                chartTemp.data.datasets[0].data.unshift(datum);
                                // if (chartTemp.data.datasets[0].data.length > 40) chartTemp.data.datasets[0].data.pop(); // Mods
                                chartVolt.update();
                                chartPower.update();
                                chartTemp.update();
                                chartHumidity.update();
                            }, index * 700);
                        });
                    }
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }

        chartVolt = drawChart('chartVolt', [], 'Voltage', 'timestamp', 'voltage', 'rgba(255, 219, 37, 1)', 'rgba(255, 219, 37, 0.5)', 'Time (GMT+7)', 'Voltage (Volt)');
        chartPower = drawChart('chartPower', [], 'Power', 'timestamp', 'power', 'rgba(255, 78, 33, 1)', 'rgba(255, 78, 33, 0.7)', 'Time (GMT+7)', 'Power (Watt)');        
        chartTemp = drawChart('chartTemp', [], 'Temp', 'timestamp', 'temp', 'rgba(255, 143, 15, 1)', 'rgba(255, 143, 15, 0.7)', 'Time (GMT+7)', 'Temperature (°C)');
        chartHumidity = drawChart('chartHumidity', [], 'Humidity', 'timestamp', 'humidity', 'rgba(104, 188, 243, 1)', 'rgba(104, 188, 243, 0.7)', 'Time (GMT+7)', 'Humidity (%)'); 

        $(document).ready(function() {
            @if ($session)
                {!! 'loadPage();' !!}
            @endif

            $(".station-date").on("click", function() {
                if (!$(this).hasClass('active')) {
                    station_date($(this).attr('date'), this);
                }
            });

            $(".station-session").on("click", function() {
                if (!$(this).hasClass('active')) {
                    station_session(this);
                }
            });

            $("#here-modal").fireModal({
                title: 'Update Paused',
                body: '\
                    <div class="text-center pt-2">\
                        <div id="icon-submit">\
                            <i class="fal fa-circle-question text-primary" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>\
                        </div>\
                        <h6 id="feedback-delete" class="font-weight-normal">Are you still here?</h6>\
                        <a id="no-button" class="btn btn-danger text-white mt-2">No</a>\
                        <a id="yes-button" class="btn btn-success text-white mt-2 ml-1">Yes</a>\
                    </div>',
                center: true
            });

            var timer;
            var focus = true;
            window.addEventListener("blur", function() {
                if (intervalPage) {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        document.title = "Update Paused - Piper Nigrum";
                        focus = false;
                    }, 60000);
                }
            });

            window.addEventListener("focus", function() {
                if (focus) {
                    clearTimeout(timer);
                } else {
                    $('#here-modal').click();
                    $("#duration-value").addClass('freeze');
                    intervalPage = clearInterval(intervalPage);
                    focus = true;
                }
            });
            
            $("#no-button").click(function() {
                $("span[aria-hidden='true']").click();
            });

            $("#yes-button").click(function() {
                document.title = "{{ $station->name }} - Piper Nigrum";
                $("span[aria-hidden='true']").click();
                loadPage();
            });
        });
    </script>

    <script>
        var state = false;

        window.addEventListener('DOMContentLoaded', function() {
            updateColumnClass();
        });

        window.addEventListener('resize', function() {
            updateColumnClass();
        });

        function updateColumnClass() {
            var section_body = document.querySelector('.section-body');

            var columns = document.querySelectorAll('.custom-column');
            if (columns.length > 0) {
                columns.forEach(function(column) {
                    if (section_body.offsetWidth < 1040) {
                        state = true;
                        column.classList.remove('col-lg-3');
                        column.classList.add('col-lg-4');
                    } else {
                        state = false;
                        column.classList.remove('col-lg-4');
                        column.classList.add('col-lg-3');
                    }
                });
            }

            var column_select = document.querySelector('.custom-column-select');
            if (state) {
                column_select.classList.remove('col-lg-3');
                column_select.classList.add('col-lg-12');
            } else {
                column_select.classList.remove('col-lg-12');
                column_select.classList.add('col-lg-3');
            }
        }
    </script>
@endpush