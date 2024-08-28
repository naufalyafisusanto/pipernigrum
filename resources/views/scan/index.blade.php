@extends('layouts.main')

@push('style')
    <style>
        .main-col {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            width: 12rem;
        }

        .main-row {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }

        .main-col-2 {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            width: 20rem;
        }

        .input-text {
            max-width: none;
        }

        @media only screen and (max-width: 767px) {
            .input-text {
                max-width: 220px;
            }
        }
    </style>
@endpush()

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card card-statistic-1">
                    <div class="card-body mx-4 my-1 p-4">
                        <div class="rounded row main-row p-3" style="border: 3px solid rgb(25, 29, 33);">
                            <div class="main-col mx-auto">
                                <i class="fa-light fa-qrcode text-dark m-5 pt-2" style="scale: 7;"></i>
                                    <a class="btn btn-success text-white mt-5 mb-4" @if (request()->secure()) href={{ route('scan.scanner') }} @else id="scanner-modal" @endif>Open Scanner</a>
                            </div>
                            <div class="mx-auto">
                                <form id="form-result" method="POST" action={{ route('scan.form') }}>
                                    @csrf
                                    <div class="main-col-2 mt-4">
                                        <h5>QRCode Scan Result</h5>
                                        <textarea type="text" id="result" name="result" class="form-control input-text disabled" style="min-height: 65px; padding-right: 30px;" placeholder="Paste here"></textarea>
                                        <div id="feedback-input" class="invalid-feedback input-text font-weight-normal" style="font-size: 12px; display: none;"></div>
                                        <a id="submit-button" class="btn btn-primary text-white mt-3 disabled">Submit</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>            
    </div>
@endsection

@push('script')
    <script>
        var submitButton = document.getElementById('submit-button');
        var resultInput = document.getElementById('result');
        var feedbackInput = document.getElementById("feedback-input");
        var checked = false;
        var checking = false;
        var lastValue;

        $("#scanner-modal").fireModal({
            title: 'Scanner Page Instructions',
            body: '\
                <ul style="padding-left: 25px">\
                    <li>Your connection is using HTTP.</li>\
                    <li>You\'ll be redirected to an HTTPS connection.</li>\
                    <li>If the HTTPS/SSL certificate warning page occurs, go to advanced option and click continue.</li>\
                </ul>', 
            center: true,
            buttons: [
                {
                    text: 'Continue',
                    class: 'btn btn-warning btn-shadow text-white',
                    handler: function(modal) {
                        window.location.href = '{{ str_replace('http:', 'https:', route('scan.scanner')) }}';
                    }
                }
            ]
        });

        document.addEventListener("DOMContentLoaded", function() {
            var element = document.querySelector('.modal-body');
            if (element) {
                element.classList.add('pb-0');
            }
            var element = document.querySelector('.modal-footer');
            if (element) {
                element.classList.add('pt-0');
            }
        });

        $("#result").on("change keyup paste click", function() {
            if (!checked) {
                var value = resultInput.value;
                if (!value || value.length != 44) {
                    resultInput.classList.remove('is-valid');
                    resultInput.classList.add('is-invalid');
                    feedbackInput.style.display = "";
                    feedbackInput.innerHTML = 'Length does not match.';
                    submitButton.classList.add('disabled');
                } else if (!checking) {
                    checking = true;
                    resultInput.classList.remove('is-invalid');
                    feedbackInput.style.display = "none";
                    $.get('{{ route('scan.verify').'?result=' }}' + encodeURIComponent(value), function(data){
                        if (data.msg == "OK") {
                            lastValue = value;
                            resultInput.readOnly = true;
                            resultInput.classList.add('is-valid');
                            submitButton.classList.remove('disabled');
                            checked = true;
                        } else {
                            resultInput.classList.remove('is-valid');
                            resultInput.classList.add('is-invalid');
                            feedbackInput.style.display = "";
                            feedbackInput.innerHTML = 'Result is invalid!';
                            checking = false;
                        }
                    });               
                }
            }
        });

        submitButton.addEventListener('click', function() {
            submitButton.classList.add('btn-progress', 'disabled');
            var form = document.getElementById("form-result");
            form.submit();
        });
    </script>
@endpush