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
                        <h5 class="text-center color-pipernigrum">Manage Users</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive" style="min-height: 300px">
                            <table class="table table-striped" id="table-user">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Username</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Action</th>
                                        <th class="text-center">Role</th>
                                        <th class="text-center">Date Created</th>
                                        <th class="text-center">Last Login</th>
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

    @if (session()->has('addSuccess'))
        <script>
            $(document).ready(function() {
                iziToast.success({
                    title: 'Add User',
                    message: 'User {{ '@'.session()->get('addSuccess') }} succesfully added!',
                    timeout: 5000,
                    overlayColor: 'rgba(0, 0, 0, 1.0)',
                    position: 'topRight'
                });
            });
        </script>
    @endif

    @if (session()->has('editSuccess'))
        <script>
            $(document).ready(function() {
                iziToast.success({
                    title: 'Edit User',
                    message: 'User {{ '@'.session()->get('editSuccess') }} succesfully edited!',
                    timeout: 5000,
                    overlayColor: 'rgba(0, 0, 0, 1.0)',
                    position: 'topRight'
                });
            });
        </script>
    @endif

    <script>
        function deleteUser(userId) {
            setTimeout(function() {
                $.ajax({              
                    url: '{{ route('users.delete') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId
                    },
                    type: 'POST',
                    async: true,
                    success: function(response) {
                        $("#fire-modal-1 span[aria-hidden='true']").click();
                        if (response.msg == "OK") {
                            iziToast.success({
                                title: 'Delete User',
                                message: 'User @' + response.username + ' has been deleted!',
                                timeout: 7000,
                                overlayColor: 'rgba(0, 0, 0, 1.0)',
                                position: 'topRight'
                            });
                            loadUserTable();
                        } else {
                            iziToast.error({
                                title: 'Delete User',
                                message: response.info,
                                timeout: 7000,
                                overlayColor: 'rgba(0, 0, 0, 1.0)',
                                position: 'topRight'
                            });
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
            var parentElement = actionButton.closest('.button-user');
            if (actionButton.attr('user-action') == 'edit') {
                window.location.href = '{{ route('users.edit') }}' + '?id=' + parentElement.attr('id');
            } else if (actionButton.attr('user-action') == 'delete') {
                var usernameTd = parentElement.closest('tr').find('td').eq(1);
                $('#username-delete').html('@' +  usernameTd.html());
                $('#delete-modal').click();

                $("#fire-modal-1 span[aria-hidden='true']").off().click(function() {
                    $("#delete-button").off();
                });

                $("#cancel-button").off().removeClass('disabled').click(function() {
                    $("#fire-modal-1 span[aria-hidden='true']").click();
                });

                $("#delete-button").off().removeClass('disabled btn-progress').click(function() {
                    $('#cancel-button').addClass('disabled');
                    $(this).addClass('disabled btn-progress');
                    deleteUser(parentElement.attr('id'));
                });  
            }
        }

        $("#delete-modal").fireModal({
            title: 'Delete User',
            body: '\
                <div class="text-center pt-2">\
                    <div id="icon-submit">\
                        <i class="fal fa-circle-exclamation text-warning" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>\
                    </div>\
                    <h6 id="feedback-delete" class="font-weight-normal">Are you sure to delete user <span id="username-delete" class="text-primary"></span>?<br><span class="text-danger">This action is irreversible!</span></h6>\
                    <a id="cancel-button" class="btn btn-success text-white mt-2 mr-1">Cancel</a>\
                    <a id="delete-button" class="btn btn-danger text-white mt-2">Delete</a>\
                </div>',
            center: true
        });

        document.addEventListener("DOMContentLoaded", function() {
            var elements = document.querySelectorAll('.col-sm-12, .col-md-5');
            elements.forEach(function(element) {
                element.classList.add('p-0');
            });
        });

        $("#fire-modal-1 span[aria-hidden='true']").click(function() {
            stationButton.classList.remove('btn-progress', 'disabled');
        });

        function loadUserTable() {
            $("#table-user").dataTable({
                ajax: {
                    url: '{{ route('users.table') }}',
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
                bLengthChange : false,
                bDestroy: true,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'username', name: 'username'},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    {data: 'role', name: 'role'},
                    {data: 'date_created', name: 'date_created'},
                    {data: 'last_login', name: 'last_login'}
                ],
                drawCallback: function (settings) {
                    $('.button-user .button-action').on("click", function() {
                        handleAction(this);
                    });
                },
                dom: " <'row'<'col-sm-12 col-md-6 p-0 toolbar'><'col-sm-12 col-md-6 p-0'f>><'row'<'col-sm-12 p-0'tr>><'row'<'col-sm-12 col-md-5 p-0'i><'col-sm-12 col-md-7 p-0'p>>",
                initComplete: function() {
                    $("div.toolbar").html('<a id="add-button" class="btn btn-primary text-white">Add User</a>');
                    $("#add-button").on("click", function() {
                        window.location.href = '{{ route('users.add') }}';
                    })
                }
            });
        }

        loadUserTable();
    </script>
@endpush