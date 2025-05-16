@extends('layouts.main')

@push('stylesheet')
    <link rel="stylesheet" href="/assets/css/download/daterangepicker.css">
    <link rel="stylesheet" href="/assets/css/download/select2.min.css">
    <link rel="stylesheet" href="/assets/css/download/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="/assets/css/download/select.bootstrap4.css">
@endpush

@push('style')
    <style>
        @media (min-width: 769px) {
            .select-session {
                min-height: 180.35px;
            }

            .body-input {
                height: 495.55px
            }

            .card-input {
                min-height: 630.75px
            }
        }

        @media (max-width: 769px) {
            .label-check {
                margin-top: .2rem;
            }
        }

        .dataTable thead tr th {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }

        .vertical-center td {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>
@endpush

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card card-input">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Download Option</h5>
                    </div>
                    <div class="card-body pt-3 pb-0 body-input">
                        <div class="form-group">
                            <label>Select Station</label>
                            <select class="form-control select2 select-station">
                                @foreach ($select_stations as $station)
                                    <option value={{ $station->id }}>{{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Date Range</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control daterange-cus date-range bg-white" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Select Session</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="checkAllSession" disabled>
                                <label class="form-check-label label-check" for="checkAllSession">All Sessions</label>
                            </div>
                            <select class="form-control select-session" multiple="" data-height="100%" disabled></select>
                            <small id="feedbackSession" class="form-text font-weight-normal text-muted">Please select the station and date range first!</small>
                        </div>
                    </div>
                    <div class="card-footer text-center pt-2">
                        <a id="preview-button" class="btn btn-warning text-white disabled mr-2 my-1" style="min-width: 70px;">Preview</a>
                        <br class="d-inline d-sm-none">
                        <a id="csv-button" class="btn btn-primary text-white disabled download-button mr-2 my-1" style="min-width: 70px;">Download CSV</a>
                        <a id="xlsx-button" class="btn btn-primary text-white disabled download-button my-1" style="min-width: 70px;">Download XLSX</a>
                    </div>
                </div>
            </div> 
            <div class="col-12 col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Download Preview</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive" style="min-height: 150px">
                            <table class="table table-striped dataTable" id="table-preview" style="width: 100em">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Session Id</th>
                                        <th class="text-center">Timestamp</th>
                                        <th class="text-center">Voltage (V)</th>
                                        <th class="text-center">Current (A)</th>
                                        <th class="text-center">Power (Watt)</th>
                                        <th class="text-center">Frequency (Hz)</th>
                                        <th class="text-center">Power Factor</th>
                                        <th class="text-center">Temp (°C)</th>
                                        <th class="text-center">Humidity (%)</th>
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
    <script src="/assets/js/download/daterangepicker.js"></script>
    <script src="/assets/js/download/select2.full.min.js"></script>
    <script src="/assets/js/download/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="/assets/js/download/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/assets/js/download/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>

    <script>
        $('.daterange-cus').daterangepicker({
            locale: {format: 'YYYY-MM-DD'},
            drops: 'down',
            opens: 'right'
        });

        var state_select = false;
        var state_range = false;

        function getSession() {
            if (state_select && state_range) {
                var dateRange = $('.date-range').val().split(" - ");
                $.ajax({
                    url: '{{ route('download.session') }}',
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $('.select-station').val(),
                        start_date: dateRange[0],
                        end_date: dateRange[1]
                    },
                    async: true,
                    success: function(response){
                        if (response.msg == "OK") {
                            $('#checkAllSession').removeAttr('disabled');
                            $('#feedbackSession').prop('hidden', true);
                            $('.select-session').removeAttr('disabled').html(response.data.view);
                        } else {
                            $('#checkAllSession').attr('disabled', true).prop('checked', false);
                            $('#feedbackSession').prop('hidden', false).removeClass('text-muted').addClass('text-danger').html(response.data.info);
                            $('.select-session').attr('disabled', true).html("");
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            }
        }

        function updateButton() {
            if ($('.select-session option:selected').length > 0) {
                $('.download-button').removeClass('disabled');
                $('#preview-button').removeClass('disabled');
            } else {
                $('.download-button').addClass('disabled');
                $('#preview-button').addClass('disabled');
            }
        }

        function checkIfAllSelected() {
            var allSelected = $('.select-session option').length === $('.select-session option:selected').length;
            $('#checkAllSession').prop('checked', allSelected);
        }

        function sanitizeFilename(filename) {
            return filename.replace(/[\/\?<>\\:\*\|"]/g, '');
        }
        
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select a station"
            });

            $('.select2').val(null).trigger('change');

            $('.select-station').on('change', function() {
                state_select = true;
                getSession();
            });

            $('.date-range').on('change', function() {
                state_range = true;
                getSession();
            });

            $('#checkAllSession').change(function() {
                if($(this).is(':checked')) {
                    $('.select-session option').prop('selected', true);
                } else {
                    $('.select-session option').prop('selected', false);
                }
                updateButton();
            });

            $('.select-session').on('change', function() {
                $('#checkAllSession').prop('checked', false);
                updateButton();
                checkIfAllSelected();
            });

            $('.download-button').click(function() {
                $(this).addClass('btn-progress');
                $('.download-button').addClass('disabled');
                const element = this;
                let url;
                if ($(this).attr('id') == "csv-button") url = '{{ route('download.datacsv') }}';
                else if ($(this).attr('id') == "xlsx-button") url = '{{ route('download.dataxlsx') }}';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: { 
                        sessions: $('.select-session').val() 
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    async: true,
                    success: function(response) {
                        $(element).removeClass('btn-progress');
                        $('.download-button').removeClass('disabled');
                        var url = window.URL.createObjectURL(response);
                        var a = document.createElement('a');
                        a.href = url;
                        var dateRange = $('.date-range').val().split(" - ");
                        var fileName = $('.select-station option:selected').text() + ' from ' + dateRange[0] + ' to ' + dateRange[1];
                        a.download = sanitizeFilename(fileName);
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            });

            $("#table-preview").dataTable({
                ajax : {
                    url: '{{ route('download.preview') }}',
                    type: 'GET',
                    data: {
                        sessions: [0]
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                },
                processing : true,
                serverSide : true,
                bFilter : false,
                bLengthChange : false
            });

            $('#preview-button').click(function() {
                $('#preview-button').addClass('disabled btn-progress');
                $("#table-preview").dataTable({
                    ajax : {
                        url: '{{ route('download.preview') }}',
                        type: 'GET',
                        data: {
                            sessions: $('.select-session').val()
                        },
                        complete: function () {
                            if ($('.select-session option:selected').length > 0) {
                                $('#preview-button').removeClass('disabled btn-progress');
                            }
                            $('#feedbackSession').prop('hidden', false).removeClass('text-danger').addClass('text-muted').html("Preview limit at 500 rows.");
                        },
                        error: function(xhr, error, code) {
                            if (xhr.status === 401) {
                                window.location.href = '{{ route('auth.login') }}';
                            }
                        }                        
                    },
                    processing : true,
                    serverSide : true,
                    bFilter : false,
                    bLengthChange : false,
                    bDestroy: true,
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'id_session', name: 'id_session'},
                        {data: 'timestamp', name: 'timestamp'},
                        {data: 'voltage', name: 'voltage'},
                        {data: 'current', name: 'current'},
                        {data: 'power', name: 'power'},
                        {data: 'frequency', name: 'frequency'},
                        {data: 'power_factor', name: 'power_factor'},
                        {data: 'temp', name: 'temp'},
                        {data: 'humidity', name: 'humidity'},
                    ]
                });    
            });
        });
    </script>
@endpush