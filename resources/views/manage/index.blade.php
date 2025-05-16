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

        .dataTable thead tr th {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
    </style>
@endpush

@section('main-body')
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Manage Stations</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive" style="min-height: 450px">
                            <table class="table table-striped" id="station-table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Station Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                        <th class="text-center">IP Address</th>
                                        <th class="text-center">MAC Address</th>
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
    <script src="/assets/js/manage/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="/assets/js/manage/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/assets/js/manage/datatables.net-select-bs4/js/select.bootstrap4.min.js"></script>

    <script>
        function handleRunAction(runButton, selectButton, id, action) {
            runButton.addClass('disabled btn-progress');
            selectButton.addClass('disabled');
            setTimeout(function() {
                $.ajax({              
                    url: '{{ route('manage.action') }}',
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
                            rowElement.find('td').eq(2).html(response.data.status);
                            var stationButton = rowElement.find('td').eq(3);
                            stationButton.html(response.data.button);
                            stationButton.find('.button-action').each(function() {
                                $(this).on("click", function() {
                                    handleAction(this);
                                })
                            });
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

        document.addEventListener("DOMContentLoaded", function() {
            var elements = document.querySelectorAll('.col-sm-12, .col-md-5');
            elements.forEach(function(element) {
                element.classList.add('p-0');
            });
        });

        $("#station-table").dataTable({
            ajax: {
                url: '{{ route('manage.table') }}',
                type: 'GET',
                error: function(xhr, error, code) {
                    if (xhr.status === 403) {
                        alert('Session expired. Redirecting to login page.');
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            },
            processing : true,
            serverSide : true,
            order : [[2, 'asc']],
            bLengthChange : false,
            bDestroy: true,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'ip_address', name: 'ip_address'},
                {data: 'mac_address', name: 'mac_address', orderable: false, searchable: false},
            ],
            drawCallback: function (settings) {
                $('.button-station .button-action').on("click", function() {
                    handleAction(this);
                });
            }
        });       
    </script>

    <script>
        function edit_action(id, action) {
            if (action == 'params') {
                window.open('{{ route('manage.params') }}' + '?id=' + id, '_blank');
            } else if (action == 'station') {
                window.open('{{ route('manage.station') }}' + '?id=' + id, '_blank');
            }  
        }
    </script>
@endpush