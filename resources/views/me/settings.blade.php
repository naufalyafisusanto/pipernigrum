@extends('layouts.main')

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Form User Settings</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <form action={{ route('me.submit') }} id="form-user" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder={{ $user->username }} value={{ $user->username }} required>
                                <div id="feedback-input-username" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder='{{ $user->name }}' value='{{ $user->name }}' required>
                                <div id="feedback-input-name" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="changePassword">
                                    <label class="form-check-label label-check" for="changePassword">Change Password</label>
                                </div>
                                <input type="text" name="password" class="form-control" id="password" placeholder="New Password" disabled>
                                <div id="feedback-input-password" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            @can('admin')
                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="custom-select" name="role" id="role" style="font-size: 14px;" required>
                                        <option @if(!$user->admin){!! 'selected' !!}@endif value="operator">Operator</option>
                                        <option @if($user->admin){!! 'selected' !!}@endif value="administrator">Administrator</option>
                                    </select>
                                </div>
                            @endcan
                        </form>
                        <small id="passwordWarning" class="form-text text-muted" style="display: none">You will be logged out and need to re-log in with new password.</small>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a id="submit-button" class="btn btn-primary text-white disabled">Submit</a>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection

@push('script') 
    <script>
        const submits = new Map();

        function allowSubmit() {
            let result = false;
            
            if (submits.size > 0) {
                result = true;
                for (let value of submits.values()) {
                    result = result && value;
                }
            }
            if (result) {
                $("#submit-button").removeClass('disabled');
            } else {
                $("#submit-button").addClass('disabled');
            }
        }

        $('#changePassword').change(function() {
            if($(this).is(':checked')) {
                $('#password').attr('disabled', false)
                $('#passwordWarning').css('display', '');
                submits.set('password', false);
                allowSubmit();
            } else {
                $('#password').attr('disabled', true).removeClass('is-valid is-invalid').val('');
                $('#passwordWarning').css('display', 'none');
                submits.delete('password');
                allowSubmit();
            }
        });

        $('#role').change(function() {
            if($(this).val() != '{{ strtolower($user->role()) }}') {
                submits.set('role', true);
                allowSubmit();
            } else {
                submits.delete('role');
                allowSubmit();
            }
        });

        var checking = false;
        var timerUsername;
        var regexUsername = /[^A-Za-z0-9_]/g;

        $("#username").on("keyup", function() {
            clearTimeout(timerUsername);

            timerUsername = setTimeout(() => {
                var value = $('#username').val();
                if (!value) {
                    $('#username').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-username').removeClass('valid-feedback').addClass('invalid-feedback').text('The username cannot be empty!');
                    submits.set('username', false);
                    allowSubmit();
                } else if (regexUsername.test(value)) {
                    $('#username').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-username').removeClass('valid-feedback').addClass('invalid-feedback').text('The username must be alphanumeric and underscore!');
                    submits.set('username', false);
                    allowSubmit();
                } else if (value.length < 8) {
                    $('#username').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-username').removeClass('valid-feedback').addClass('invalid-feedback').text('The username must be at least 8 characters!');
                    submits.set('username', false);
                    allowSubmit();
                } else if (!checking) {
                    checking = true;
                    $.get('{{ route('me.username').'?username=' }}' + encodeURIComponent(value), function(response){
                        if (response.msg == "OK") {
                            $('#username').removeClass('is-invalid').addClass('is-valid');
                            $('#feedback-input-username').removeClass('invalid-feedback').addClass('valid-feedback').text('');
                            checking = false;
                            submits.set('username', true);
                            allowSubmit();
                        } else {
                            $('#username').removeClass('is-valid').addClass('is-invalid');
                            $('#feedback-input-username').removeClass('valid-feedback').addClass('invalid-feedback').text("The username '" + response.data + "' already exists!");
                            checking = false;
                            submits.set('username', false);
                            allowSubmit();
                        }
                    });               
                }
            }, 1000);
        });

        var timerName;

        $("#name").on("keyup", function() {
            clearTimeout(timerName);

            timerName = setTimeout(() => {
                var value = $('#name').val();
                if (!value) {
                    $('#name').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-name').removeClass('valid-feedback').addClass('invalid-feedback').text('The name cannot be empty!');
                    submits.set('name', false);
                    allowSubmit();
                } else if (value.length < 8) {
                    $('#name').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-name').removeClass('valid-feedback').addClass('invalid-feedback').text('The name must be at least 8 characters!');
                    submits.set('name', false);
                    allowSubmit();
                } else {
                    $('#name').removeClass('is-invalid').addClass('is-valid');
                    $('#feedback-input-name').removeClass('invalid-feedback').addClass('valid-feedback').text('');
                    submits.set('name', true);
                    allowSubmit();
                }
            }, 1000);
        });

        var timerPassword;
        var regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])/;

        $("#password").on("keyup", function() {
            clearTimeout(timerPassword);

            timerPassword = setTimeout(() => {
                var value = $('#password').val();
                if (!value) {
                    $('#password').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-password').removeClass('valid-feedback').addClass('invalid-feedback').text('The password cannot be empty!');
                    submits.set('password', false);
                    allowSubmit();
                } else if (!regexPassword.test(value)) {
                    $('#password').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-password').removeClass('valid-feedback').addClass('invalid-feedback').text('The password must be at least one uppercase, lowercase, digit, and special character!');
                    submits.set('password', false);
                    allowSubmit();
                } else if (value.length < 8) {
                    $('#password').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-password').removeClass('valid-feedback').addClass('invalid-feedback').text('The password must be at least 8 characters!');
                    submits.set('password', false);
                    allowSubmit();
                } else {
                    $('#password').removeClass('is-invalid').addClass('is-valid');
                    $('#feedback-input-password').removeClass('invalid-feedback').addClass('valid-feedback').text('');
                    submits.set('password', true);
                    allowSubmit();
                }
            }, 1000);
        });

        $("#submit-button").on("click", function() {
            $("#form-user").submit();
        });
    </script>
@endpush