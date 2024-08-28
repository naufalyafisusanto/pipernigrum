@extends('layouts.main')

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Form Params</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <form id="formData"> 
                            @csrf
                            <input type="hidden" name="id" value="{{ $station->id }}">
                            <div class="form-group">
                                <label>Speed</label>
                                <div class="form-row p-2" style="border:1px solid #ced4da; border-radius: 0.25rem; margin: 0 0.7px;">    
                                    <div class="form-group col-md-12 col-sm-12 col-12 mb-1">
                                        <label for="fan">Fan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control params params-input params-int" id="speed-fan" name="speed-fan" placeholder={{ $params['speed-fan'] }} value={{ $params['speed-fan'] }} min="10" max="255">
                                            <div class="input-group-append">
                                                <div class="input-group-text" style="width: 45px;padding: 10px 0px;justify-content: center; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">PWM</div>
                                            </div>
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Maximum Value</label>
                                <div class="form-row p-2" style="border:1px solid #ced4da; border-radius: 0.25rem; margin: 0 0.7px;">    
                                    <div class="form-group col-md-12 col-sm-12 col-12 mb-1">
                                        <label for="heater-power">Heater Power</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control params params-input params-int" id="max-heater-power" name="max-heater-power" placeholder={{ $params['max-heater-power'] }} value={{ $params['max-heater-power'] }} min="20" max="100">
                                            <div class="input-group-append">
                                                <div class="input-group-text" style="width: 45px;padding: 10px 0px;justify-content: center; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">%</div>
                                            </div>
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                        <small class="form-text text-muted">*Please be careful, 100% means 1400 Watts of heater power.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Set Point</label>
                                <div class="form-row p-2" style="border:1px solid #ced4da; border-radius: 0.25rem; margin: 0 0.7px;">    
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="temperature">Temp Fast</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control params params-input params-int" id="set-temperature-fast" name="set-temperature-fast" placeholder={{ $params['set-temperature-fast'] }} value={{ $params['set-temperature-fast'] }} min="40.0" max="80.0">
                                            <div class="input-group-append">
                                                <div class="input-group-text" style="width: 45px;padding: 10px 0px;justify-content: center; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">&deg;C</div>
                                            </div>
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="temperature">Temp Medium</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control params params-input params-int" id="set-temperature-medium" name="set-temperature-medium" placeholder={{ $params['set-temperature-medium'] }} value={{ $params['set-temperature-medium'] }} min="40.0" max="80.0">
                                            <div class="input-group-append">
                                                <div class="input-group-text" style="width: 45px;padding: 10px 0px;justify-content: center; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">&deg;C</div>
                                            </div>
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="temperature">Temp Low</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control params params-input params-int" id="set-temperature-low" name="set-temperature-low" placeholder={{ $params['set-temperature-low'] }} value={{ $params['set-temperature-low'] }} min="40.0" max="80.0">
                                            <div class="input-group-append">
                                                <div class="input-group-text" style="width: 45px;padding: 10px 0px;justify-content: center; border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">&deg;C</div>
                                            </div>
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>PID Control</label>
                                <div class="form-row p-2" style="border:1px solid #ced4da; border-radius: 0.25rem; margin: 0 0.7px;">
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="proportional">Proportional</label>
                                        <div class="input-group">
                                            <input type="text" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;" class="form-control params params-input params-float" id="pid-proportional" name="pid-proportional" placeholder={{ $params['pid-proportional'] }} value={{ $params['pid-proportional'] }} min="0.0" max="5000.0">
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="integral">Integral</label>
                                        <div class="input-group">
                                            <input type="text" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;" class="form-control params params-input params-float" id="pid-integral" name="pid-integral" placeholder={{ $params['pid-integral'] }} value={{ $params['pid-integral'] }} min="0.0" max="5000.0">
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-4 mb-1">
                                        <label for="derivative">Derivative</label>
                                        <div class="input-group">
                                            <input type="text" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;" class="form-control params params-input params-float" id="pid-derivative" name="pid-derivative" placeholder={{ $params['pid-derivative'] }} value={{ $params['pid-derivative'] }} min="0.0" max="5000.0">
                                            <div class="invalid-feedback" style="font-size: 13px"></div>
                                        </div>
                                        <small class="form-text text-muted params-feedback" style="font-size:13px"></small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a id="submit-button" class="btn btn-primary text-white disabled" style="min-width: 70px;">Submit</a>
                    </div>
                </div>
            </div> 
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Load Params</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <div class="form-group">
                            <label>Configuration File (.json)</label>
                            <div class="custom-file">
                                <input type="file" name="loadjson" class="custom-file-input" id="loadjsonInput" accept=".json" required>
                                <label class="custom-file-label" for="customFile" style="padding-left: 15px">Choose file</label>
                                <div class="invalid-feedback" style="font-size: 13px; margin-top: 10px;">File must have a .json extension.</div>
                            </div>
                            <a id="load-error-modal"></a>
                        </div>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a class="btn btn-warning text-white disabled" style="min-width: 70px;" id="load-button" onclick="loadJSON()">Load</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Backup Params</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <div class="form-group">
                            <label>Configuration Name</label>
                            <div class="input-group">
                                <input type="text" name="filename" class="form-control" id="filenameInput" placeholder="Filename" value={{ 'params-'.str_replace(' ', '-', strtolower($station->name)) }} required>
                                <div class="input-group-append">
                                    <div class="input-group-text" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem;">.json</div>
                                </div>
                                <div id="feedback-input-filename" class="invalid-feedback" style="font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a class="btn btn-success text-white" style="min-width: 70px;" id="backup-button" onclick="backupJSON()">Backup</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const loadjsonInput = document.getElementById('loadjsonInput');
        const loadButton = document.getElementById('load-button');

        $('#loadjsonInput').change(function(e){
            if (e.target.files[0]) {
                var fileName = e.target.files[0].name;
                $('.custom-file-label').html(fileName);
                var fileExtension = fileName.split('.').pop();
                if (fileExtension == 'json') {
                    loadButton.classList.remove('disabled');
                    loadjsonInput.classList.remove('is-invalid');
                } else {
                    loadjsonInput.classList.add('is-invalid');
                    loadButton.classList.add('disabled');
                }
            } else {
                $('.custom-file-label').html('Choose file');
                loadjsonInput.classList.remove('is-invalid');
                loadButton.classList.add('disabled');
            }
        });

        const filenameInput = document.getElementById('filenameInput');
        const feedbackfilenameInput = document.getElementById("feedback-input-filename");
        const backupButton = document.getElementById('backup-button');
        var timer;

        $("#filenameInput").on("keyup", function() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                var value = filenameInput.value;
                var feedback = "";
                if (!value) {
                    feedback = 'Filename cannot be empty!';
                } else if (!/^[A-Za-z0-9\-]+$/.test(value)) {
                    feedback = 'Filename must be alphanumeric and hyphen (-).';
                } else if (/[^A-Za-z0-9]$/.test(value)) {
                    feedback = 'Filename cannot end with a special character.';
                }

                if (feedback) {
                    backupButton.classList.add('disabled');
                    filenameInput.classList.add('is-invalid');
                    feedbackfilenameInput.classList.add('invalid-feedback');
                    feedbackfilenameInput.innerHTML = feedback;
                } else {
                    backupButton.classList.remove('disabled');
                    filenameInput.classList.remove('is-invalid');
                    feedbackfilenameInput.classList.remove('invalid-feedback');
                    feedbackfilenameInput.innerHTML = "";
                }
            }, 1000);
        });
    </script>

    <script>
        $("#load-error-modal").fireModal({
            title: 'Load Error',
            body: '<span id="load-error-desc"></span>',
            center: true
        });
        var load_error_modal = document.getElementById('load-error-modal');
        var load_error_desc = document.getElementById('load-error-desc');

        const timers = new Map();
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

        function handleInputChange(inputElement) {
            const timer = timers.get(inputElement);
            if (timer) {
                clearTimeout(timer);
            }

            const formParent = $(inputElement).closest('.input-group').closest('.form-group');
            const feedbackElement = formParent.find('.params-feedback');
            const feedbackError = formParent.find('.invalid-feedback');
            const placeholderValue = $(inputElement).attr('placeholder');
            const actualValue = $(inputElement).val();
            
            const newTimer = setTimeout(() => {
                if (actualValue === '') {
                    $(inputElement).val(placeholderValue);
                    feedbackElement.text('');
                    feedbackError.text('');
                    $(inputElement).removeClass('is-invalid');
                    submits.delete(inputElement);
                    allowSubmit();
                    return;
                }

                if ($(inputElement).hasClass('params-float')) {
                    if (!/^-?\d+\.\d+$/.test(actualValue)) {
                        feedbackError.text('The input must be a float!');
                        $(inputElement).addClass('is-invalid');
                        feedbackElement.text('');
                        submits.set(inputElement, false);
                        allowSubmit();
                        return;
                    }

                    var inputValue = parseFloat($(inputElement).val());
                    var minValue = parseFloat($(inputElement).attr('min'));
                    var maxValue = parseFloat($(inputElement).attr('max'));

                    if(!(inputValue >= minValue && inputValue <= maxValue) && !isNaN(minValue) && !isNaN(maxValue)) {
                        feedbackError.text(`The input must be between ${minValue} and ${maxValue}!`);
                        $(inputElement).addClass('is-invalid');
                        feedbackElement.text('');
                        submits.set(inputElement, false);
                        allowSubmit();
                        return;
                    }     
                }

                if ($(inputElement).hasClass('params-int')) {
                    if (!/^-?\d+$/.test(actualValue)) {
                        feedbackError.text('The input must be an integer!');
                        $(inputElement).addClass('is-invalid');
                        feedbackElement.text('');
                        submits.set(inputElement, false);
                        allowSubmit();
                        return;
                    }

                    var inputValue = parseInt($(inputElement).val());
                    var minValue = parseInt($(inputElement).attr('min'));
                    var maxValue = parseInt($(inputElement).attr('max'));

                    if(!(inputValue >= minValue && inputValue <= maxValue) && !isNaN(minValue) && !isNaN(maxValue)) {
                        feedbackError.text(`The input must be between ${minValue} and ${maxValue}!`);
                        $(inputElement).addClass('is-invalid');
                        feedbackElement.text('');
                        submits.set(inputElement, false);
                        allowSubmit();
                        return;
                    }
                }

                if (inputValue != placeholderValue) {
                    feedbackElement.text(`${placeholderValue} → ${inputValue}`);
                    $(inputElement).val(inputValue);
                } else {
                    feedbackElement.text('');
                }

                feedbackError.text('');
                $(inputElement).removeClass('is-invalid');
                submits.set(inputElement, true);
                allowSubmit();                
            }, 1500);
            timers.set(inputElement, newTimer);
        }

        $('.params-input').on("keyup", function() {
            handleInputChange(this);
        });

        function handleInputLoad() {
            $('.params-input').each(function() {
                handleInputChange(this);
            });
        }

        const placeholders = {};

        document.querySelectorAll('.params').forEach(input => {
            if (input.classList.contains('params-float')) {
                placeholders[input.id] = parseFloat(input.placeholder);
            } else if (input.classList.contains('params-int')) {
                placeholders[input.id] = parseInt(input.placeholder);
            } else {
                placeholders[input.id] = input.placeholder;
            }
        });
        
        function loadJSON() {
            const fileInput = document.getElementById('loadjsonInput');
            const formData = document.getElementById('formData');
                        
            const file = fileInput.files[0];
            const reader = new FileReader();
            
            reader.onload = function(event) {
                try {
                    const jsonData = JSON.parse(event.target.result);

                    const paramInputs = Array.from(formData.querySelectorAll('.params'));
                    const paramIds = paramInputs.map(input => input.id);

                    const missingParams = paramIds.filter(paramId => !jsonData[paramId]);
                    if (missingParams.length > 0) {
                        $('#load-button').addClass('disabled');
                        load_error_desc.innerHTML = `Missing parameters from '${file.name}' : <ul><li>${missingParams.join('</li><li>')}</li></ul>`;
                        load_error_modal.click();
                        return;
                    }

                    for (const paramId of paramIds) {
                        formData.elements[paramId].value = jsonData[paramId];
                    }

                    $('#load-button').addClass('disabled');
                    iziToast.success({
                        title: 'Load Success',
                        message: 'Loaded from ' + file.name,
                        timeout: 5000,
                        overlayColor: 'rgba(0, 0, 0, 1.0)',
                        position: 'topRight'
                    });
                    handleInputLoad();
                } catch (error) {
                    load_error_desc.innerHTML = `Invalid JSON format on '${file.name}'`;
                    load_error_modal.click();
                }
            };
            
            reader.readAsText(file);
        }

        function backupJSON() {
            const filenameInput = document.getElementById('filenameInput');
            const json = JSON.stringify(placeholders, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filenameInput.value + '.json';
            document.body.appendChild(a);
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>

    <script>
        $("#submit-button").fireModal({
            title: 'Writing Parameters',
            body: '\
                <div class="text-center pt-2">\
                    <div id="icon-submit">\
                        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>\
                    </div>\
                    <h6 id="feedback-form" class="font-weight-normal">Connecting to Station</h6>\
                    <a id="cancel-button" class="btn btn-danger text-white mt-2 mr-1" style="display: none;">Cancel</a>\
                    <a id="retry-button" class="btn btn-success text-white mt-2" style="display: none;">Retry</a>\
                </div>',
            center: true
        });

        var submitButton = document.getElementById('submit-button');
        var iconSubmit = document.getElementById('icon-submit');
        var feedbackForm = document.getElementById('feedback-form');
        var cancelButton = document.getElementById('cancel-button');
        var retryButton = document.getElementById('retry-button');
        var submit_state = false;

        submitButton.addEventListener('click', function() {
            iconSubmit.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackForm.innerHTML = 'Connecting to Station';
            cancelButton.style.display = "none";
            retryButton.style.display = "none";
            retryButton.innerHTML = 'Retry';
            submitButton.classList.add('btn-progress', 'disabled');
            submit_state = false;

            setTimeout(function() {
                check();
            }, 3000);           
        });
        
        cancelButton.addEventListener('click', function() {
            $("span[aria-hidden='true']").click();
            submit_state = false;
        });

        retryButton.addEventListener('click', function() {
            if (!submit_state) {
                iconSubmit.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
                feedbackForm.innerHTML = 'Connecting to Station';
                cancelButton.style.display = "none";
                retryButton.style.display = "none";
                retryButton.innerHTML = 'Retry';
                
                setTimeout(function() {
                    check();
                }, 3000);
            } else {
                submit();
            }
        });

        $("span[aria-hidden='true']").click(function() {
            submitButton.classList.remove('btn-progress', 'disabled');
            submit_state = false;
        });

        function check() {
            if ($('#fire-modal-2').is(':visible')) {
                $.ajax({
                    url: '{{ route('manage.running').'?id='.$station->id }}',
                    type: 'GET',
                    async: true,
                    success: function(response){
                        if (response.msg == "STOPPED") {
                            submit();
                        } else if (response.msg == "RUNNING") {
                            submit_state = true;
                            iconSubmit.innerHTML = '<i class="fal fa-circle-exclamation text-warning" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackForm.innerHTML = 'Station is running!';
                            cancelButton.style.display = "";
                            retryButton.style.display = "";
                            retryButton.innerHTML = 'Continue';
                        } else {
                            iconSubmit.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackForm.innerHTML = 'Station is offline!';
                            cancelButton.style.display = "none";
                            retryButton.style.display = "";
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            } else {
                submitButton.classList.remove('btn-progress', 'disabled');
                submit_state = false;
            }
        }

        function submit() {
            iconSubmit.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackForm.innerHTML = 'Sending Parameters';
            cancelButton.style.display = "none";
            retryButton.style.display = "none";
            retryButton.innerHTML = 'Retry';
            setTimeout(function() {
                if ($('#fire-modal-2').is(':visible')) {
                    $.ajax({
                        url: '{{ route('manage.params.submit') }}',
                        type: 'POST',
                        data: $('#formData').serialize(),
                        async: true,
                        success: function(response){
                            if (response.msg == "OK") {
                                iconSubmit.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                                feedbackForm.innerHTML = 'Parameters successfully sent!<br>Redirect in <span id="count">5</span>';
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
                            } else {
                                iconSubmit.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                                feedbackForm.innerHTML = 'An error occured while sending parameters!<br>' + response.data.info;
                                cancelButton.style.display = "none";
                                retryButton.style.display = "";
                            }
                        },
                        error: function(xhr, error, code) {
                            if (xhr.status === 401) {
                                window.location.href = '{{ route('auth.login') }}';
                            }
                        }
                    });
                } else {
                    submitButton.classList.remove('btn-progress', 'disabled');
                    submit_state = false;
                }
            }, 3000);   
        }
    </script>
@endpush