@extends('layouts.main')

@push('style')
    <link rel="stylesheet" href="/assets/css/manage/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="/assets/css/manage/select.bootstrap4.css">
    
    <style>
        .dropdown-menu.select-action.none-hover:hover {
            background-color: #fff;
        }
        
        .vertical-center td {
            vertical-align: middle;
            padding: 9px;
        }

        th, td {
            vertical-align: middle;
            white-space: nowrap;
        }

        @keyframes rotateCW {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .rotate-cw {
            animation: rotateCW 5s linear infinite;
        }

        @keyframes rotateCCW {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(-360deg);
            }
        }

        .rotate-ccw {
            animation: rotateCCW 5s linear infinite;
        }

        .dataTables_scrollBody {
            min-height: 280px;
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
            height: 330px;
        }
    </style>
@endpush

@section('main-body')
    <div class="section-body">        
        <div class="row">
            <div class="col-lg-8">
                <div class="card" style="height: 420px;">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Final & Initial Mass</h5>
                    </div>
                    <div class="card-body chartWrapper">
                        <div class="chartAreaWrapper">
                            <div class="chartAreaWrapper2">
                                <canvas id="mass-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class="card card-statistic-2">
                            <div class="card-chart">
                                <canvas id="energy-chart" height="80"></canvas>
                            </div>
                            <div class="card-icon" style="background-color: #00cc66">
                                <i class="far fa-bolt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Energy <small>(<span id="energy-title">0000-00-00 to 0000-00-00</span>)</small></h4>
                                </div>
                                <div class="card-body">
                                    <span id="energy-sum">0</span><small> kWh</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-6">
                        <div class="card card-statistic-2">
                            <div class="card-stats">
                                <div class="card-stats-title">Statistics -
                                    <div class="dropdown d-inline">
                                        <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="javascript:;" id="statistics-select">Lifetime</a>
                                        <ul class="dropdown-menu dropdown-menu-sm select-data">
                                            <li><a class="dropdown-item statistics-station active" station="0">Lifetime</a></li>
                                            @empty($stations)
                                                @php
                                                    $stations = stations();
                                                @endphp
                                            @endempty
                                            @forelse ($stations as $row_station)
                                                <li><a class="dropdown-item statistics-station" href="javascript:;" station={{ $row_station->id }}>{{ $row_station->name }}</a></li>
                                            @empty
                                                <li><a class="dropdown-item disabled">No Station</a></li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-stats-items">
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count" id="session-value">0</div>
                                        <div class="card-stats-item-label scroll-text" style="text-overflow: unset">Session</div>
                                    </div>
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count" id="energy-value">0</div>
                                        <div class="card-stats-item-label scroll-text" style="text-overflow: unset">Energy (kWh)</div>
                                    </div>
                                    <div class="card-stats-item">
                                        <div class="card-stats-item-count" id="duration-value">0</div>
                                        <div class="card-stats-item-label scroll-text" style="text-overflow: unset">Duration (hr)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-icon" style="background-color: #8d6753">
                                <i class="far fa-weight-hanging"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Production</h4>
                                </div>
                                <div class="card-body"><span id="production-value">0</span> <small>Kg</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Station Status</h5>
                    </div>
                    <div class="card-body" style="min-height: 364px;">
                        <canvas id="station-chart" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Station Shortcut</h5>
                    </div>
                    <div class="card-body pt-2" style="min-height: 364px;">
                        <div class="table-responsive" style="min-height: 300px">
                            <table class="table table-striped" id="station-table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Station Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="/assets/js/dashboard/chart.js/dist/Chart.min.js"></script>
    <script src="/assets/js/manage/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="/assets/js/manage/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/assets/js/manage/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>

    <script>
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var position = 'right';
        var font_size = 18;
        var table_height = "280px";
        if (screenWidth < 1200) {
            position = 'bottom';
        }
        if (screenWidth < 800) {
            font_size = 15;
        }
        if (screenWidth < 500) {
            table_height = "400px";
            $('.chartAreaWrapper').css('overflow-x', 'scroll');
            $('.chartAreaWrapper2').css('width', '600px');
        }

        var ctxMass = document.getElementById("mass-chart").getContext('2d');
        var chartMass = new Chart(ctxMass, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Final Mass',
                    data: [],
                    borderWidth: 2,
                    backgroundColor: 'rgba(141, 103, 83, .6)',
                    borderWidth: 0,
                    borderColor: 'transparent',
                    pointBorderWidth: 0,
                    pointRadius: 2.5,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(141, 103, 83, .6)',
                }, {
                    label: 'Initial Mass',
                    data: [],
                    borderWidth: 2,
                    backgroundColor: 'rgba(255, 219, 37, .5)',
                    borderWidth: 0,
                    borderColor: 'transparent',
                    pointBorderWidth: 0 ,
                    pointRadius: 2.5,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(255, 219, 37, .5)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom',
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: '#f2f2f2',
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return value + ' Kg';
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                            tickMarkLength: 15,
                        }
                    }]
                },
            }
        });

        var energy_chart = document.getElementById("energy-chart").getContext('2d');

        var energy_chart_bg_color = energy_chart.createLinearGradient(0, 0, 0, 70);
        energy_chart_bg_color.addColorStop(0, 'rgba(0, 204, 102, .2)');
        energy_chart_bg_color.addColorStop(1, 'rgba(0, 204, 102, 0)');

        var chartEnergy = new Chart(energy_chart, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Energy',
                    data: [],
                    backgroundColor: energy_chart_bg_color,
                    borderWidth: 3,
                    borderColor: 'rgba(0, 204, 102, 1)',
                    pointBorderWidth: 0,
                    pointBorderColor: 'transparent',
                    pointRadius: 3,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(0, 204, 102, 1)',
                }]
            },
            options: {
                layout: {
                    padding: {
                        top: 2,
                        bottom: 2,
                        left: -1,
                        right: -1
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            beginAtZero: true,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            display: false,
                        },
                        ticks: {
                            display: false
                        }
                    }]
                },
            }
        });

        Chart.pluginService.register({
            beforeDraw: function(chart) {
                if (chart.config.options.elements.center) {
                    var ctx = chart.chart.ctx;

                    var centerConfig = chart.config.options.elements.center;
                    var fontStyle = centerConfig.fontStyle || 'Arial';
                    var txt = centerConfig.text;
                    var color = centerConfig.color || '#000';
                    var maxFontSize = centerConfig.maxFontSize || 75;
                    var sidePadding = centerConfig.sidePadding || 20;
                    var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
                    ctx.font = "30px " + fontStyle;

                    var stringWidth = ctx.measureText(txt).width;
                    var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                    var widthRatio = elementWidth / stringWidth;
                    var newFontSize = Math.floor(30 * widthRatio);
                    var elementHeight = (chart.innerRadius * 2);

                    var fontSizeToUse = Math.min(newFontSize, elementHeight, maxFontSize);
                    var minFontSize = centerConfig.minFontSize;
                    var lineHeight = centerConfig.lineHeight || 25;
                    var wrapText = false;

                    if (minFontSize === undefined) {
                        minFontSize = 20;
                    }

                    if (minFontSize && fontSizeToUse < minFontSize) {
                        fontSizeToUse = minFontSize;
                        wrapText = true;
                    }

                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                    var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                    ctx.font = "600 " + fontSizeToUse + "px " + fontStyle;
                    ctx.fillStyle = color;

                    if (!wrapText) {
                        ctx.fillText(txt, centerX, centerY);
                        return;
                    }

                    var words = txt.split(' ');
                    var line = '';
                    var lines = [];

                    for (var n = 0; n < words.length; n++) {
                        var testLine = line + words[n] + ' ';
                        var metrics = ctx.measureText(testLine);
                        var testWidth = metrics.width;
                        if (testWidth > elementWidth && n > 0) {
                        lines.push(line);
                        line = words[n] + ' ';
                        } else {
                        line = testLine;
                        }
                    }

                    centerY -= (lines.length / 2) * lineHeight;

                    for (var n = 0; n < lines.length; n++) {
                        ctx.fillText(lines[n], centerX, centerY);
                        centerY += lineHeight;
                    }
                    ctx.fillText(line, centerX, centerY);
                }
            }
        });

        const plugin = {
            id: 'emptyDoughnut',
            afterDraw(chart, args, options) {
                const {datasets} = chart.data;
                const {color, width, radiusDecrease} = options;
                let hasData = false;

                for (let i = 0; i < datasets.length; i += 1) {
                    const dataset = datasets[i];
                    hasData |= dataset.data.length > 0;
                }

                if (!hasData) {
                    const {chartArea: {left, top, right, bottom}, ctx} = chart;
                    const centerX = (left + right) / 2;
                    const centerY = (top + bottom) / 2;
                    const r = Math.min(right - left, bottom - top) / 2;

                    ctx.beginPath();
                    ctx.lineWidth = width || 2;
                    ctx.strokeStyle = color || 'rgba(255, 128, 0, 0.5)';
                    ctx.arc(centerX, centerY, (r - radiusDecrease || 0), 0, 2 * Math.PI);
                    ctx.stroke();
                }
            }
        };

        var ctxStation = document.getElementById("station-chart").getContext('2d');
        var chartStation = new Chart(ctxStation, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#47c363',
                        '#fc544b',
                        '#ff37f5',
                        '#fc544b',
                        '#ffc107',
                        '#808080',
                    ],
                    label: 'Status',
                    borderColor: 'rgba(0, 0, 0, 0)',
                    borderWidth: 0
                }, {
                    data: [],
                    backgroundColor: [
                        '#47c363',
                        '#fc544b',
                        '#ff37f5',
                        '#fc544b',
                        '#ffc107',
                        '#808080',
                    ],
                    label: 'Running',
                    borderColor: 'rgba(0, 0, 0, 0)',
                    borderWidth: 0
                }],
                labels: [
                    'Running',
                    'Stopped',
                    'Stopped (Insert)',
                    'Stopped (Brake)',
                    'Stopped (Eject)',
                    'Disconnected',
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: position,
                },
                elements: {
                    center: {
                        text: 'None',
                        color: 'rgba(103, 119, 239, 0.9)',
                        fontStyle: 'arial',
                        sidePadding: 20,
                        minFontSize: font_size,
                        maxFontSize: font_size,
                        lineHeight: 25
                    }
                },
                plugins: {
                    emptyDoughnut: {
                        color: 'rgba(103, 119, 239, 0.5)',
                        width: 2,
                        radiusDecrease: 20
                    }
                }
            },
            plugins: [plugin]
        });

        function handleRunAction(runButton, selectButton, id, action) {
            runButton.addClass('disabled btn-progress');
            selectButton.addClass('disabled');
            setTimeout(function() {
                $.ajax({              
                    url: '{{ route('dashboard.action') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_station: id,
                        action: action
                    },
                    type: 'POST',
                    async: true,
                    success: function(response) {
                        if (response.msg == "OK") {
                            iziToast.success({
                                title: response.data.name,
                                message: response.data.info,
                                timeout: 7000,
                                overlayColor: 'rgba(0, 0, 0, 1.0)',
                                position: 'topRight'
                            });
                            selectButton.removeClass('btn-primary btn-warning btn-danger btn-success disabled').addClass('btn-primary');
                            selectButton.text('Select');
                            runButton.removeClass('btn-primary btn-progress').addClass('btn-secondary');
                            var rowElement = selectButton.closest('.station-row');
                            rowElement.find('td').eq(1).html(response.data.status);
                            var stationButton = rowElement.find('td').eq(2);
                            stationButton.html(response.data.button);
                            stationButton.find('.button-action').each(function() {
                                $(this).on("click", function() {
                                    handleAction(this);
                                })
                            });

                            chartStation.data.datasets[0].data = response.data.chart_station.chart_0;
                            chartStation.data.datasets[1].data = response.data.chart_station.chart_1;
                            chartStation.update();
                        } else {
                            iziToast.error({
                                title: response.data.name,
                                message: response.data.info,
                                timeout: 7000,
                                overlayColor: 'rgba(0, 0, 0, 1.0)',
                                position: 'topRight'
                            });
                            selectButton.removeClass('disabled');
                            runButton.removeClass('btn-progress disabled');
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            }, 2000);
        }

        function handleAction(actionButton) {
            var actionButton = $(actionButton);
            var parentElement = actionButton.closest('.button-station');
            var runButton = parentElement.find('.button-run');
            var selectButton = parentElement.find('.button-select');
            runButton.removeClass('disabled', 'btn-secondary').addClass('btn-primary');
            selectButton.removeClass('btn-primary btn-warning btn-danger btn-success btn-magenta');
            selectButton.text(actionButton.text());
            if (actionButton.hasClass('text-success')) selectButton.addClass('btn-success');
            else if (actionButton.hasClass('text-warning')) selectButton.addClass('btn-warning');
            else if (actionButton.hasClass('text-danger')) selectButton.addClass('btn-danger');
            else if (actionButton.hasClass('text-magenta')) selectButton.addClass('btn-magenta');
            
            runButton.off("click");
            runButton.on("click", function() {
                handleRunAction(runButton, selectButton, parentElement.attr('station-id'), actionButton.attr('station-action'));
            });  
        }

        $("#station-table").dataTable({
            ajax: {
                url: '{{ route('dashboard.table') }}',
                type: 'GET',
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            },
            processing : true,
            serverSide : true,
            order : [[1, 'asc']],
            bLengthChange : false,
            bFilter : false,
            bPaginate : false,
            bInfo : false,
            bDestroy : true,
            columns : [
                {data: 'name', name: 'name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            drawCallback : function (settings) {
                $('.button-station .button-action').on("click", function() {
                    handleAction(this);
                });
            },
            scrollY : table_height,
            scrollCollapse : true,
            scrollX : true,
        });  

        function loadPage() {
            $.ajax({
                url: '{{ route('dashboard.load') }}',
                type: 'GET',
                async: true,
                success: function(response) {
                    var length = response.data.chart_mass.labels.length;
                    chartMass.data.labels = Array.from({ length: length }, () => 'None');
                    chartMass.data.datasets[1].data = Array.from({ length: length }, () => 0);
                    chartMass.data.datasets[0].data = Array.from({ length: length }, () => 0);
                    chartMass.update();

                    chartEnergy.data.labels = Array.from({ length: length }, () => 'None');
                    chartEnergy.data.datasets[0].data = Array.from({ length: length }, () => 0);
                    chartEnergy.update();
                    
                    chartMass.data.labels = response.data.chart_mass.labels;
                    chartMass.data.datasets[1].data = response.data.chart_mass.initial_mass;
                    chartMass.data.datasets[0].data = response.data.chart_mass.final_mass;
                    chartMass.update();
                    
                    chartEnergy.data.labels = response.data.chart_energy.labels;
                    chartEnergy.data.datasets[0].data = response.data.chart_energy.energy;
                    chartEnergy.update();

                    $("#energy-title").html(response.data.chart_energy.title);
                    $("#energy-sum").html(response.data.chart_energy.sum.toFixed(2));

                    chartStation.data.datasets[0].data = response.data.chart_station.chart_0;
                    chartStation.data.datasets[1].data = response.data.chart_station.chart_1;
                    chartStation.options.elements.center.text = response.data.chart_station.chart_text;
                    chartStation.update();

                    $("#session-value").html(response.data.statistics.session);
                    $("#energy-value").html(response.data.statistics.energy);
                    $("#duration-value").html(response.data.statistics.duration);
                    $("#production-value").html(response.data.statistics.production);
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }

        function statistics_station(id, element) {
            $("#session-value").html('0');
            $("#energy-value").html('0');
            $("#duration-value").html('0');
            $("#production-value").html('0');

            setTimeout(function() {
                $.ajax({
                    url: '{{ route('dashboard.statistics').'?id=' }}' + id,
                    type: 'GET',
                    async: true,
                    success: function(response) {
                        $("#statistics-select").html($(element).html());
                        $(element).closest('.select-data').find('.statistics-station').removeClass('active');
                        $(element).addClass('active');
                        
                        $("#session-value").html(response.data.statistics.session);
                        $("#energy-value").html(response.data.statistics.energy);
                        $("#duration-value").html(response.data.statistics.duration);
                        $("#production-value").html(response.data.statistics.production);
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            }, 500);
        }

        $(document).ready(function() {
            loadPage();

            $(".statistics-station").on("click", function() {
                if (!$(this).hasClass('active')) {
                    statistics_station($(this).attr('station'), this);
                }
            });

            $('#station-table_info').closest('.col-sm-12').removeClass('col-md-5').addClass('col-md-12');

            function autoScrollHorizontal(element) {
                var containerWidth = $(element).outerWidth();
                var contentWidth = $(element).prop('scrollWidth');
                
                if (contentWidth > containerWidth + 3) {
                    var spaces = new Array(Math.floor(containerWidth/6)).join('&nbsp;');
                    $(element).append(spaces);
                    const contentWidth = element.scrollWidth;
                    let scrollPosition = -20;
                    const scrollSpeed = 0.5;
                    const scrollInterval = 30;
                    
                    function scroll() {
                        scrollPosition += scrollSpeed;
                        if (scrollPosition >= contentWidth) {
                            scrollPosition = -20;
                        }
                        $(element).scrollLeft(scrollPosition);
                    }
                    
                    setInterval(scroll, scrollInterval);
                }
            }
            
            $('.scroll-text').each(function(index, element) {
                autoScrollHorizontal(element);
            });
        });
    </script>
@endpush