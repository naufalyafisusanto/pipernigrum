@extends('layouts.main')

@section('main-body')
    <div class="section-body">
        <div class="row" style="justify-content: center;">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Form Config</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <div class="form-group">
                            <label>Station Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Station</div>
                                </div>
                                <input type="text" class="form-control" id="station-input" value="{{ explode(' ', $station->name)[1] }}" placeholder="{{ explode(' ', $station->name)[1] }}" style="border-top-right-radius: .25rem;border-bottom-right-radius: .25rem;" required>
                                <div id="station-feedback" class="invalid-feedback" style="font-size: 13px; padding-left: 68px;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Station New IP Address</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="192.168.22." readonly>
                                <select class="custom-select" id="new-ip-input" style="max-width: 100px;">
                                    @foreach ($free as $host)
                                        <option value="{{ $host }}">{{ $host }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Station Current IP Address</label>
                            <input type="text" class="form-control" id="current-ip-input" value="{{ $station->ip_address }}" readonly required>
                        </div>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a id="station-button" class="btn btn-primary text-white disabled" style="min-width: 70px;">Submit</a>
                    </div>
                </div>
            </div> 
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Firmware Update</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <form id="firmware-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $station->id }}">
                            <div class="form-group">
                                <label>Firmware File (.bin,.bin.gz)</label>
                                <div class="custom-file">
                                    <input type="file" name="firmware" class="custom-file-input" id="firmware-input" accept=".bin,.bin.gz" required>
                                    <label class="custom-file-label" for="customFile" style="padding-left: 15px">Choose file</label>
                                    <div class="invalid-feedback" id="firmware-feedback" style="font-size: 13px; margin-top: 10px;">File must have a .bin or .bin.gz extension.</div>
                                </div>
                                <a id="update-error-modal"></a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a class="btn btn-warning text-white disabled" style="min-width: 70px;" id="update-button">Update</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header header-center">
                        <h5 class="text-center color-pipernigrum">Delete Station</h5>
                    </div>
                    <div class="card-body pt-3 pb-0">
                        <form id="">
                            <div class="form-group">
                                <label>Delete Confirmation</label>
                                <input type="text" class="form-control" id="delete-input" placeholder="{{ "Type '".$station->name."' to confirm." }}" required>
                                <div id="delete-feedback-input" class="invalid-feedback" style="font-size: 13px;"></div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-right pt-2">
                        <a class="btn btn-danger text-white disabled" style="min-width: 70px;" id="delete-button">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const firmwareInput = document.getElementById('firmware-input');
        const firmwareFeedback = document.getElementById('firmware-feedback');
        const updateButton = document.getElementById('update-button');

        $('#firmware-input').change(function(e){
            if (e.target.files[0]) {
                var fileName = e.target.files[0].name;
                $('.custom-file-label').html(fileName);
                if (fileName.endsWith('.bin') || fileName.endsWith('.bin.gz')) {
                    firmwareInput.classList.remove('is-invalid');
                    firmwareInput.classList.add('is-valid');
                    updateButton.classList.remove('disabled');
                    firmwareFeedback.innerHTML = "The station will reboot automatically after update.<br>WARNING: This action is irreversible!";
                    firmwareFeedback.style.display = "block";
                } else {
                    firmwareInput.classList.add('is-invalid');
                    firmwareInput.classList.remove('is-valid');
                    updateButton.classList.add('disabled');
                    firmwareFeedback.innerHTML = "File must have a .bin or .bin.gz extension.";
                }
            } else {
                $('.custom-file-label').html('Choose file');
                firmwareInput.classList.remove('is-invalid');
                firmwareInput.classList.remove('is-valid');
                updateButton.classList.add('disabled');
            }
        });

        $("#update-button").fireModal({
            title: 'Updating Firmware',
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

        var iconSubmitModal1 = document.querySelector('#fire-modal-1 #icon-submit');
        var feedbackFormModal1 = document.querySelector('#fire-modal-1 #feedback-form');
        var retryButtonModal1 = document.querySelector('#fire-modal-1 #retry-button');

        updateButton.addEventListener('click', function() {
            iconSubmitModal1.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackFormModal1.innerHTML = 'Connecting to Station';
            retryButtonModal1.style.display = "none";
            retryButtonModal1.innerHTML = 'Retry';
            updateButton.classList.add('btn-progress', 'disabled');

            setTimeout(function() {
                checkModal1();
            }, 3000);         
        });

        $("#fire-modal-1 span[aria-hidden='true']").click(function() {
            updateButton.classList.remove('btn-progress', 'disabled');
        });

        retryButtonModal1.addEventListener('click', function() {
            iconSubmitModal1.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackFormModal1.innerHTML = 'Connecting to Station';
            retryButtonModal1.style.display = "none";
            retryButtonModal1.innerHTML = 'Retry';
            
            setTimeout(function() {
                checkModal1();
            }, 3000);
        });

        function checkModal1() {
            if ($('#fire-modal-1').is(':visible')) {
                $.ajax({
                    url: '{{ route('manage.running').'?id='.$station->id }}',
                    type: 'GET',
                    async: true,
                    success: function(response){
                        if (response.msg == "STOPPED") {
                            feedbackFormModal1.innerHTML = 'Writing Firmware to Station<br>Do not close this page or the station will be damaged!';
                            setTimeout(function() {
                                updateFirmware()
                            }, 3000);
                        } else if (response.msg == "RUNNING") {
                            
                            iconSubmitModal1.innerHTML = '<i class="fal fa-circle-exclamation text-warning" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal1.innerHTML = 'Update couldn\'t begin because station is running!';
                            updateButton.classList.remove('btn-progress', 'disabled');
                        } else {
                            iconSubmitModal1.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal1.innerHTML = 'Station is offline!';
                            retryButtonModal1.style.display = "";
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            } else {
                updateButton.classList.remove('btn-progress', 'disabled');
            }
        }

        function updateFirmware() {
            var form = $('#firmware-form')[0];
            var formData = new FormData(form);
            $.ajax({
                url: '{{ route('manage.station.update') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                async: true,
                success: function(response){
                    if (response.msg == "OK") {
                        feedbackFormModal1.innerHTML = 'Rebooting Station';
                        setTimeout(function() {  
                            iconSubmitModal1.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';                     
                            feedbackFormModal1.innerHTML = 'Firmware successfully updated!<br>Close pop up in <span id="update-count">5</span>';
                            var second = 5;
                            var count = document.getElementById('update-count');
                            var countInterval = setInterval(function() {
                                second -= 1;
                                if (second < 0) {
                                    $("span[aria-hidden='true']").click();
                                    clearInterval(countInterval);
                                } else {
                                    count.innerHTML = second;
                                }
                            }, 1000);
                        }, 7000);
                    } else if (response.msg == "FAIL") {
                        iconSubmitModal1.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                        feedbackFormModal1.innerHTML = 'Firmware Update Failed';
                        updateButton.classList.remove('btn-progress', 'disabled');
                    } else {
                        iconSubmitModal1.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                        feedbackFormModal1.innerHTML = 'Error when Updating Firmware<br>' + response.data.info;
                        updateButton.classList.remove('btn-progress', 'disabled');
                    }
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }
    </script>

    <script>
        const stationInput = document.getElementById('station-input');
        const stationFeedback = document.getElementById('station-feedback');
        const newIPInput = document.getElementById('new-ip-input');
        const currentIPInput = document.getElementById('current-ip-input');
        const stationButton = document.getElementById('station-button');
        var checking = false;
        var timer;
        var regex = /[^A-Za-z 0-9]/g;
        var submits = new Map();

        function allowSubmit() {
            let result = false;
            if (submits.size > 0) {
                result = true;
                for (let value of submits.values()) {
                    result = result && value;
                }
            }
            if (result) {
                $("#station-button").removeClass('disabled');
            } else {
                $("#station-button").addClass('disabled');
            }
        }

        $("#station-input").on("keyup", function() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                var value = stationInput.value;
                if (!value) {
                    stationInput.classList.remove('is-valid');
                    stationInput.classList.add('is-invalid');
                    stationFeedback.classList.add('invalid-feedback');
                    stationFeedback.classList.remove('valid-feedback');
                    stationFeedback.innerHTML = 'The name cannot be empty!';
                    submits.set(stationInput, false);
                    allowSubmit();
                } else if (regex.test(value)) {
                    stationButton.classList.add('disabled');
                    stationInput.classList.remove('is-valid');
                    stationInput.classList.add('is-invalid');
                    stationFeedback.classList.add('invalid-feedback');
                    stationFeedback.classList.remove('valid-feedback');
                    stationFeedback.innerHTML = 'The name must be alphanumeric and space!';
                    submits.set(stationInput, false);
                    allowSubmit();
                } else if (value == stationInput.placeholder) {
                    stationInput.classList.remove('is-valid', 'is-invalid');
                    submits.delete(stationInput);
                    allowSubmit();
                } else if (!checking) {
                    checking = true;
                    stationInput.classList.remove('is-invalid');
                    $.get('{{ route('manage.station.name').'?name=' }}' + encodeURIComponent('Station ' + value),
                        function(response){
                            if (response.msg == "OK") {
                                stationButton.classList.remove('disabled');
                                stationInput.classList.add('is-valid');
                                stationFeedback.classList.add('valid-feedback');
                                stationFeedback.classList.remove('invalid-feedback');
                                stationFeedback.innerHTML = "Result : 'Station " + value + "'";
                                submits.set(stationInput, true);
                            } else {
                                stationInput.classList.remove('is-valid');
                                stationInput.classList.add('is-invalid');
                                stationFeedback.classList.add('invalid-feedback');
                                stationFeedback.classList.remove('valid-feedback');
                                stationFeedback.innerHTML = "The name '" + response.data + "' already exists!";
                                submits.set(stationInput, false);
                                
                            }
                            checking = false;
                            allowSubmit();
                    });               
                }
            }, 1000);
        });

        $('#new-ip-input').on('change', function() {
            var new_ip = '192.168.22.' + newIPInput.value;
            if (new_ip == currentIPInput.value) {
                submits.delete(newIPInput);
                allowSubmit();
            } else {
                submits.set(newIPInput, true);
                allowSubmit();
            }
        });

        $("#station-button").fireModal({
            title: 'Configuring Station',
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

        var iconSubmitModal2 = document.querySelector('#fire-modal-2 #icon-submit');
        var feedbackFormModal2 = document.querySelector('#fire-modal-2 #feedback-form');
        var retryButtonModal2 = document.querySelector('#fire-modal-2 #retry-button');

        stationButton.addEventListener('click', function() {
            iconSubmitModal2.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackFormModal2.innerHTML = 'Connecting to Station';
            retryButtonModal2.style.display = "none";
            retryButtonModal2.innerHTML = 'Retry';
            stationButton.classList.add('btn-progress', 'disabled');

            setTimeout(function() {
                checkModal2();
            }, 3000);         
        });

        $("#fire-modal-2 span[aria-hidden='true']").click(function() {
            stationButton.classList.remove('btn-progress', 'disabled');
        });

        retryButtonModal2.addEventListener('click', function() {
            iconSubmitModal2.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackFormModal2.innerHTML = 'Connecting to Station';
            retryButtonModal2.style.display = "none";
            retryButtonModal2.innerHTML = 'Retry';
            
            setTimeout(function() {
                checkModal2();
            }, 3000);
        });

        function checkModal2() {
            if ($('#fire-modal-2').is(':visible')) {
                $.ajax({
                    url: '{{ route('manage.running').'?id='.$station->id }}',
                    type: 'GET',
                    async: true,
                    success: function(response){
                        if (response.msg == "STOPPED") {
                            feedbackFormModal2.innerHTML = 'Sending Configuration to Station';
                            setTimeout(function() {
                                sendConf();
                            }, 3000);
                        } else if (response.msg == "RUNNING") {
                            iconSubmitModal2.innerHTML = '<i class="fal fa-circle-exclamation text-warning" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal2.innerHTML = 'Unable to configure the station while running!';
                            stationButton.classList.remove('btn-progress', 'disabled');
                        } else {
                            iconSubmitModal2.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal2.innerHTML = 'Station is offline!';
                            retryButtonModal2.style.display = "";
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            } else {
                stationButton.classList.remove('btn-progress', 'disabled');
            }
        }

        function sendConf() {
            $.ajax({
                url: '{{ route('manage.station.config') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: {{ $station->id }},
                    name: 'Station ' + stationInput.value,
                    new_ip: '192.168.22.' + newIPInput.value
                },
                async: true,
                success: function(response){
                    if (response.msg == "OK") {
                        feedbackFormModal2.innerHTML = 'Rebooting Station';
                        setTimeout(function() {  
                            iconSubmitModal2.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';                     
                            feedbackFormModal2.innerHTML = 'Station successfully configured!<br>Reload page in <span id="config-count">5</span>';
                            var second = 5;
                            var count = document.getElementById('config-count');
                            var countInterval = setInterval(function() {
                                second -= 1;
                                if (second < 0) {
                                    window.location.reload();
                                    clearInterval(countInterval);
                                } else {
                                    count.innerHTML = second;
                                }
                            }, 1000);
                        }, 7000);
                    } else {
                        iconSubmitModal2.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                        feedbackFormModal2.innerHTML = 'Error when Configuring Station<br>' + response.data.info;
                        stationButton.classList.remove('btn-progress', 'disabled');
                    }
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }
    </script>

    <script>
        const deleteInput = document.getElementById('delete-input');
        const deleteFeedback = document.getElementById('delete-feedback-input');
        const deleteButton = document.getElementById('delete-button');
        var timer_delete;

        $("#delete-input").on("keyup", function() {
            clearTimeout(timer_delete);

            timer_delete = setTimeout(() => {
                if (deleteInput.value != '{{ $station->name }}') {
                    deleteButton.classList.add('disabled');
                    deleteInput.classList.add('is-invalid');
                    deleteInput.classList.remove('is-valid');
                    deleteFeedback.innerHTML = "Type '{{ $station->name }}' to confirm deletion.";
                } else {
                    deleteButton.classList.remove('disabled');
                    deleteInput.classList.remove('is-invalid');
                    deleteInput.classList.add('is-valid');
                    deleteFeedback.innerHTML = "All station data and configurations will be deleted.<br>WARNING: This action is irreversible!";
                    deleteFeedback.style.display = "block";                         
                }
            }, 1000);
        });

        $("#delete-button").fireModal({
            title: 'Deleting Station',
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

        var iconSubmitModal3 = document.querySelector('#fire-modal-3 #icon-submit');
        var feedbackFormModal3 = document.querySelector('#fire-modal-3 #feedback-form');
        var cancelButtonModal3 = document.querySelector('#fire-modal-3 #cancel-button');
        var retryButtonModal3 = document.querySelector('#fire-modal-3 #retry-button');
        var delete_state = false;

        deleteButton.addEventListener('click', function() {
            iconSubmitModal3.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackFormModal3.innerHTML = 'Connecting to Station';
            cancelButtonModal3.style.display = "none";
            retryButtonModal3.style.display = "none";
            retryButtonModal3.innerHTML = 'Retry';
            deleteButton.classList.add('btn-progress', 'disabled');
            delete_state = false;

            setTimeout(function() {
                checkModal3();
            }, 3000);         
        });

        $("#fire-modal-3 span[aria-hidden='true']").click(function() {
            deleteButton.classList.remove('btn-progress', 'disabled');
            delete_state = false;
        });

        cancelButtonModal3.addEventListener('click', function() {
            $("span[aria-hidden='true']").click();
            delete_state = false;
        });

        retryButtonModal3.addEventListener('click', function() {
            if (!delete_state) {
                iconSubmitModal3.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
                feedbackFormModal3.innerHTML = 'Connecting to Station';
                cancelButtonModal3.style.display = "none";
                retryButtonModal3.style.display = "none";
                retryButtonModal3.innerHTML = 'Retry';
                
                setTimeout(function() {
                    checkModal3();
                }, 3000);
            } else {
                iconSubmitModal3.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
                feedbackFormModal3.innerHTML = 'Deleting Station';
                cancelButtonModal3.style.display = "none";
                retryButtonModal3.style.display = "none";
                retryButtonModal3.innerHTML = 'Retry';
                setTimeout(function() {
                    deleteStation(true);
                }, 2000);
            }
        });

        function checkModal3() {
            if ($('#fire-modal-3').is(':visible')) {
                $.ajax({
                    url: '{{ route('manage.running').'?id='.$station->id }}',
                    type: 'GET',
                    async: true,
                    success: function(response){
                        if (response.msg == "STOPPED") {
                            feedbackFormModal3.innerHTML = 'Deleting Station';
                            setTimeout(function() {
                                deleteStation();
                            }, 3000);
                        } else if (response.msg == "RUNNING") {
                            iconSubmitModal3.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal3.innerHTML = 'Unable to delete the station while running!';
                            deleteButton.classList.remove('btn-progress', 'disabled');
                            delete_state = false;
                        } else {
                            iconSubmitModal3.innerHTML = '<i class="fal fa-circle-exclamation text-warning" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackFormModal3.innerHTML = 'Station is offline!';
                            retryButtonModal3.style.display = "";
                            retryButtonModal3.innerHTML = 'Continue';
                            cancelButtonModal3.style.display = "";
                            delete_state = true;
                        }
                    },
                    error: function(xhr, error, code) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('auth.login') }}';
                        }
                    }
                });
            } else {
                deleteButton.classList.remove('btn-progress', 'disabled');
            }
        }

        function deleteStation(force_delete = false) {
            $.ajax({
                url: '{{ route('manage.station.delete') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: {{ $station->id }},
                    force: force_delete
                },
                async: true,
                success: function(response){
                    if (response.msg == "OK") {
                        if (force_delete) {
                            iconSubmitModal3.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';                     
                            feedbackFormModal3.innerHTML = 'Station successfully deleted!<br>Redirect in <span id="count">5</span>';
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
                            feedbackFormModal3.innerHTML = 'Rebooting Station';
                            setTimeout(function() {  
                                iconSubmitModal3.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';                     
                                feedbackFormModal3.innerHTML = 'Station successfully deleted!<br>Redirect in <span id="count">5</span>';
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
                            }, 7000);
                        }
                    } else {
                        iconSubmitModal3.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                        feedbackFormModal3.innerHTML = 'Error when Deleting Station<br>' + response.data.info;
                        deleteButton.classList.remove('btn-progress', 'disabled');
                    }
                    delete_state = false;
                },
                error: function(xhr, error, code) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('auth.login') }}';
                    }
                }
            });
        }
    </script>
@endpush