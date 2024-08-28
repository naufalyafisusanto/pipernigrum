@extends('layouts.main')

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Form Station</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <div class="form-group">
                            <label>Station Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Station</div>
                                </div>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name" style="border-top-right-radius: .25rem;border-bottom-right-radius: .25rem;" required>
                                <div id="feedback-input" class="invalid-feedback" style="font-size: 13px; padding-left: 68px;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Station New IP Address</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="192.168.22." readonly>
                                <select class="custom-select" name="new_ip" id="new_ip" style="max-width: 100px;">
                                    @foreach ($free as $host)
                                        <option value="{{ $host }}">{{ $host }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Station Old IP Address</label>
                            <input type="text" name="old_ip" class="form-control" id="old_ip" value="{{ $form['old_ip'] }}" readonly required>
                        </div>
                        <div class="form-group">
                            <label>Station MAC Address</label>
                            <input type="text" name="mac" class="form-control" id="mac" value="{{ $form['mac'] }}" readonly required>
                        </div>
                        
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a id="submit-modal" class="btn btn-primary text-white disabled">Submit</a>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection

@push('script')
    <script>
        var submitButton = document.getElementById('submit-modal');
        var resultInput = document.getElementById('name');
        var feedbackInput = document.getElementById("feedback-input");
        var checking = false;
        var timer;
        var regex = /[^A-Za-z 0-9]/g;

        $("#submit-modal").fireModal({
            title: 'Configuring Station',
            body: '\
                <div class="text-center pt-2">\
                    <div id="icon-submit">\
                        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>\
                    </div>\
                    <h6 id="feedback-form" class="font-weight-normal">Connecting to Station</h6>\
                    <a id="retry-button" class="btn btn-success text-white mt-2" style="display: none;">Retry</a>\
                </div>', 
            center: true
        });

        $("#name").on("keyup", function() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                var value = resultInput.value;
                if (!value) {
                    submitButton.classList.add('disabled');
                    resultInput.classList.remove('is-valid');
                    resultInput.classList.add('is-invalid');
                    feedbackInput.classList.add('invalid-feedback');
                    feedbackInput.classList.remove('valid-feedback');
                    feedbackInput.style.display = "";
                    feedbackInput.innerHTML = 'The name cannot be empty!';
                } else if (regex.test(value)) {
                    submitButton.classList.add('disabled');
                    resultInput.classList.remove('is-valid');
                    resultInput.classList.add('is-invalid');
                    feedbackInput.classList.add('invalid-feedback');
                    feedbackInput.classList.remove('valid-feedback');
                    feedbackInput.style.display = "";
                    feedbackInput.innerHTML = 'The name must be alphanumeric and space!';
                } else if (!checking) {
                    checking = true;
                    resultInput.classList.remove('is-invalid');
                    feedbackInput.style.display = "none";
                    $.get('{{ route('scan.name').'?name=' }}' + encodeURIComponent('Station ' + value),
                        function(response){
                            if (response.msg == "OK") {
                                submitButton.classList.remove('disabled');
                                resultInput.classList.add('is-valid');
                                feedbackInput.classList.add('valid-feedback');
                                feedbackInput.classList.remove('invalid-feedback');
                                feedbackInput.innerHTML = "Result : 'Station " + value + "'";
                                feedbackInput.style.display = "";
                                checking = false;
                            } else {
                                resultInput.classList.remove('is-valid');
                                resultInput.classList.add('is-invalid');
                                feedbackInput.classList.add('invalid-feedback');
                                feedbackInput.classList.remove('valid-feedback');
                                feedbackInput.style.display = "";
                                feedbackInput.innerHTML = "The name '" + response.data + "' already exists!";
                                checking = false;
                            }
                    });               
                }
            }, 1000);
        });
    </script>
    
    <script>
        var submitButton = document.getElementById('submit-modal');
        var iconSubmit = document.getElementById('icon-submit');
        var feedbackForm = document.getElementById('feedback-form');
        var retryButton = document.getElementById('retry-button');
        var name;
        var new_ip;
        var old_ip = document.getElementById('old_ip').value;
        var mac = document.getElementById('mac').value;
        var submit_state = false;
        var cancel_state = false;

        submitButton.addEventListener('click', function() {
            name = 'Station ' + document.getElementById('name').value;
            new_ip = "192.168.22." + document.getElementById('new_ip').value;

            iconSubmit.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackForm.innerHTML = 'Connecting to Station';
            retryButton.style.display = "none";
            submitButton.classList.add('btn-progress', 'disabled');
            cancel_state = false;

            setTimeout(function() {
                submit();
            }, 3000);           
        });

        retryButton.addEventListener('click', function() {
            if (!submit_state) {
                iconSubmit.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
                feedbackForm.innerHTML = 'Connecting to Station';
                retryButton.style.display = "none";
                cancel_state = false;
                
                setTimeout(function() {
                    submit();
                }, 3000);
            } else {
                window.location.href = '{{ route('manage.index') }}';
            }
        });

        $("span[aria-hidden='true']").click(function() {
            if (!submit_state) {
                submitButton.classList.remove('btn-progress', 'disabled');
                cancel_state = true;
            }
        });

        function submit() {
            if (!cancel_state) {
                $.ajax({
                    url: '{{ route('scan.ping').'?ip=' }}' + encodeURIComponent(old_ip),
                    type: 'GET',
                    async: true,
                    success: function(response){
                        if (response.msg == "OK") {
                            feedbackForm.innerHTML = 'Configuring Station';
                            setTimeout(function() {
                                if (!cancel_state) {
                                    $.ajax({
                                        url: '{{ route('scan.submit') }}',
                                        type: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            name: name,
                                            new_ip: new_ip,
                                            old_ip: old_ip,
                                            mac: mac
                                        },
                                        async: true,
                                        success: function(response){
                                            if (response.msg == "OK") {
                                                submit_state = true;
                                                feedbackForm.innerHTML = 'Rebooting Station';
                                                submitButton.classList.add('btn-progress', 'disabled');
                                                setTimeout(function() {
                                                    retryButton.innerHTML = 'Redirect to Manage';
                                                    retryButton.style.display = "";
                                                }, 40000);  
                                                var findInterval = setInterval(function () {
                                                    $.ajax({
                                                        url: '{{ route('scan.ping').'?ip=' }}' + encodeURIComponent(new_ip),
                                                        type: 'GET',
                                                        async: true,
                                                        success: function(response){
                                                            if (response.msg == "OK") {
                                                                clearInterval(findInterval);
                                                                iconSubmit.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                                                                feedbackForm.innerHTML = 'Station successfully configured!<br>Redirect in <span id="count">5</span>';
                                                                var second = 5;
                                                                var count = document.getElementById('count');
                                                                var countInterval = setInterval(function() {
                                                                    second -= 1;
                                                                    if (second < 0) {
                                                                        window.location.href = '{{ route('manage.index') }}';
                                                                        clearInterval(countInterval);
                                                                    } else {
                                                                        count.innerHTML = second;
                                                                    }
                                                                }, 1000);  
                                                            }
                                                        },
                                                        error: function(xhr, error, code) {
                                                            if (xhr.status === 401) {
                                                                window.location.href = '{{ route('auth.login') }}';
                                                            }
                                                        }
                                                    });
                                                }, 5000);
                                            } else {
                                                iconSubmit.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                                                feedbackForm.innerHTML = 'An error occured while configuring station!<br>' + response.data.info;
                                                retryButton.style.display = "";
                                            }
                                        },
                                        error: function(xhr, error, code) {
                                            if (xhr.status === 401) {
                                                window.location.href = '{{ route('auth.login') }}';
                                            }
                                        }
                                    });
                                }
                            }, 3000);   
                        } else {
                            iconSubmit.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackForm.innerHTML = 'Station is offline!';
                            retryButton.style.display = "";
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
    </script>
@endpush