@extends('layouts.main')

@push('style')
    <link rel="stylesheet" href="/assets/css/manage/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="/assets/css/manage/select.bootstrap4.css">

    <style>
        .vertical-center td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .dataTable thead tr th {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }

        @media screen and (max-width: 767px) {
            .toolbar {
                text-align: center;
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@section('main-body')
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Activity Logs</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive" style="min-height: 300px">
                            <table class="table table-striped" id="table-log">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Timestamp</th>
                                        <th class="text-center">Username</th>
                                        <th class="text-center">Station</th>
                                        <th class="text-center">Host</th>
                                        <th class="text-center">Entity</th>
                                        <th class="text-center">Activity</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <a id="delete-modal" class="btn btn-primary text-white" hidden></a>
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
        function loadLogTable() {
            $("#table-log").dataTable({
                ajax: {
                    url: '{{ route('logs.table') }}',
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
                order : [[1, 'desc']],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'timestamp', name: 'timestamp'},
                    {data: 'username', name: 'username'},
                    {data: 'station', name: 'station'},
                    {data: 'host', name: 'host', searchable: false},
                    {data: 'entity', name: 'entity'},
                    {data: 'activity', name: 'activity'}
                ]
            });
        }

        loadLogTable();
    </script>
@endpush