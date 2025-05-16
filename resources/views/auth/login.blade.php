@extends('layouts.initial')

@push('style')
    <style>
        .form-group {
            position: relative;
        }

        .form-control {
            background-image: none !important;
        }
        
        .form-group .show-password {
            position: absolute;
            right: 10px;
            top: 44px;
            color: #cdcdcd;
            cursor: pointer;
        }
    </style>
@endpush

@section('title')
    <title>Login - Piper Nigrum</title>
@endsection

@section('body')
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="mt-4"><img src="/assets/img/pipernigrum.png" alt="Piper Nigrum Logo" height="120px"></div>
                                <div class="mt-1" style="height: 20px;"><h6 style="color: #404040;">Piper Nigrum</h6></div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action={{ route('auth.login') }} class="needs-validation" novalidate="">
                                    @csrf
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus minlength="8">
                                        <div class="invalid-feedback">Username must be at least 8 characters long.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="control-label">Password</label>
                                        <input id="password" type="password" class="form-control" name="password" tabindex="2" required minlength="8">
                                        <i class="fa-solid fa-lightbulb-slash show-password" onmousedown="showPassword()" onmouseup="hidePassword()" onmouseleave="hidePassword()" ontouchstart="showPassword()" 
                                        ontouchend="hidePassword()"></i>
                                        <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
                                    </div>
                                </form>

                                @if ($errors->any())
                                    <div class="text-danger p-0" style="font-size:12px; font-weight: 600;">
                                        <ul class="pl-3">
                                            @foreach ($errors->all() as $error)
                                                <li style="line-height: 20px;">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if (session()->has('loginError')) 
                                    <div class="text-danger text-center p-0" style="font-size:12px; font-weight: 600;">
                                        <p style="line-height: 20px;">{{ session()->get('loginError') }}</p>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                        <div class="simple-footer">
                            Copyright &copy; 2024<br><a href="javascript:;"> Piper Nigrum - Universitas Diponegoro</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    @if (session()->has('logoutSuccess'))
        <script>
            $(document).ready(function() {
                iziToast.success({
                    title: 'Logout Success',
                    message: 'You have been logged out.',
                    timeout: 5000,
                    overlayColor: 'rgba(0, 0, 0, 1.0)',
                    position: 'topRight'
                });
            });
        </script>
    @endif

    <script>
        function showPassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.show-password');
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-lightbulb-slash');
            toggleIcon.classList.add('fa-lightbulb');
            toggleIcon.style.right = '13.3px';
        }

        function hidePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.show-password');
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-lightbulb');
            toggleIcon.classList.add('fa-lightbulb-slash');
            toggleIcon.style.right = '10px';
        }
    </script>
@endpush