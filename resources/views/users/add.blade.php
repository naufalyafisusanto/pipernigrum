@extends('layouts.main')

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Form Add User</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <form action={{ route('users.submit') }} id="form-user" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
                                <div id="feedback-input-username" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
                                <div id="feedback-input-name" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="text" name="password" class="form-control" id="password" placeholder="Password" required>
                                <div id="feedback-input-password" class="invalid-feedback" style="font-size: 13px"></div>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select class="custom-select" name="role" id="role" style="font-size: 14px;" required>
                                    <option value="operator">Operator</option>
                                    <option value="administrator">Administrator</option>
                                </select>
                            </div>
                        </form>
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
            if (submits.size == 3) {
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
                    $.get('{{ route('users.username').'?username=' }}' + encodeURIComponent(value), function(response){
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
                } else if (value.includes(' ')) {
                    $('#password').removeClass('is-valid').addClass('is-invalid');
                    $('#feedback-input-password').removeClass('valid-feedback').addClass('invalid-feedback').text('The password cannot contain spaces!');
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