@extends('layouts.initial')

@section('title')
    <title>{{ $error_code }} - Piper Nigrum</title>
@endsection

@push('style')
    <style>
        .footer-custom {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .vertical-center {
            min-height: 100%; 
            min-height: 100vh;

            display: flex;
            align-items: center;
        }
        .full-screen {
            width: 100vw;
            height: 80vh;
        }

        .error-code {
            font-size: 230px;
        }

        @media only screen and (max-width: 767px) {
            .error-code {
                font-size: 150px;
            }
        }

        body {
            background-color: white !important;
        }
    </style>
@endpush

@section('body')
    <div class="main-body">
        <div id="app">
            <section class="section">
                <div class="full-screen d-flex alignt-item-center justify-content-center align-content-center text-center flex-column">
                    <div class="display-1 font-weight-normal text-danger error-code">{{ $error_code }}</div>
                    <div class="page-description">
                        <p class="lead mb-0"><strong>{{ $error_desc }}</strong></p>
                        @if ($exception->getMessage())
                            <h6 class="text-warning mt-2 font-weight-normal">{{ "'".$exception->getMessage()."'" }}</h6>
                        @endif
                        <div class="mt-3">   
                            @auth
                                <a href={{ route('root') }}>
                                    <button class="btn btn-primary btn-lg">
                                        Back to Dashboard                                              
                                    </button>
                                </a> 
                            @endauth     
                            @guest
                                <a href={{ route('auth.index') }}>
                                    <button class="btn btn-primary btn-lg">
                                        Back to Login Page                                              
                                    </button>
                                </a> 
                            @endguest                    
                        </div>
                        </div>
                    <div class="footer fixed-bottom">
                        <div class="footer-custom my-4 mb-5">
                            <img src="/assets/img/pipernigrum.png" alt="Piper Nigrum Logo" height="80px">
                            Copyright &copy; 2024<br><a href="javascript:;"> Piper Nigrum - Universitas Diponegoro</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection