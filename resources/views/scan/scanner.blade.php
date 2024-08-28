@extends('layouts.initial')

@section('title')
    <title>Scanner - Piper Nigrum</title>
@endsection

@push('style')
    <style>
        .main-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            border: 3px solid rgb(25, 29, 33);
            padding: 20px;
        }

        .html5-qrcode-element {
            width: 12rem;
            font-size: 14px;
        }

        #reader {
            border-radius: 10px;
        }

        @media (min-width: 800px) {
            #reader {
                width: 40vw;
            }
        }

        @media (max-width: 599px) {
            #reader {
                width: 80vw;
            }
        }

        #result {
            text-align: center;
            font-size: 1.5rem;
        }

        body {
            background-color: white !important;
        }
    </style>
@endpush

@section('body')
    <div class="main-body">
        <div class="main" id="main-scanner">
            <div id="reader"></div>
            <a class="btn btn-primary" href={{ route('scan.index') }}>Back to Scan Page</a>
        </div>
        <form method="POST" id="form-result" action={{ route('scan.form') }}>
            @csrf
            <input type="text" id="result" name="result" readonly hidden></input>
        </form>
        <button id="result-modal" hidden></button>
    </div>
@endsection

@push('script')  
    <script src="/assets/js/scan/html5-qrcode.min.js"></script>

    <script>
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var size;

        $("#result-modal").fireModal({
            title: 'Processing Result',
            body: '\
                <div class="text-center pt-2">\
                    <div id="icon-result">\
                        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>\
                    </div>\
                    <h6 id="feedback-result" class="font-weight-normal">Verifying Scan Result</h6>\
                    <a id="retry-button" class="btn btn-success text-white mt-2" style="display: none;">Retry</a>\
                </div>', 
            center: true,
        });

        var iconResult = document.getElementById('icon-result');
        var feedbackResult = document.getElementById("feedback-result");
        var retryButton = document.getElementById('retry-button');

        retryButton.addEventListener('click', function() {
            document.getElementsByClassName("close")[0].click();
            document.getElementById('html5-qrcode-button-camera-start').click();
            retryButton.style.display = "none";
            iconResult.innerHTML = '<div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>';
            feedbackResult.innerHTML = 'Verifying Scan Result';
        });

        if (screenWidth > 1000) {
            size = 350;
        } else {
            size = 0.5*screenWidth;
        }
        const scanner = new Html5QrcodeScanner('reader', {
                qrbox: {
                width: size,
                height: size,
                },
            fps: 20,
            });
        scanner.render(success);

        function success(value) {    
            document.getElementById('html5-qrcode-button-camera-stop').click();
            document.getElementById('result-modal').click();

            setTimeout(function() {
                if (!value || value.length != 44) {
                    retryButton.style.display = "";
                    iconResult.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                    feedbackResult.innerHTML = 'Length does not match.';
                } else {
                    $.get('{{ route('scan.verify').'?result=' }}' + encodeURIComponent(value), function(data){
                        if (data.msg == "OK") {
                            iconResult.innerHTML = '<i class="fal fa-circle-check text-success" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackResult.innerHTML = 'Verifying Result Success<br>Redirect in <span id="count">5</span>';
                            var second = 5;
                            var count = document.getElementById('count');
                            var countInterval = setInterval(function() {
                                second -= 1;
                                if (second < 0) {
                                    var inputResult = document.getElementById('result');
                                    inputResult.value = value;
                                    var formResult = document.getElementById('form-result');
                                    formResult.submit();
                                    clearInterval(countInterval);
                                } else {
                                    count.innerHTML = second;
                                }
                            }, 1000);  
                        } else {
                            retryButton.style.display = "";
                            iconResult.innerHTML = '<i class="fal fa-circle-xmark text-danger" style="scale: 6; margin-top: 35px; margin-bottom: 50px;"></i>';
                            feedbackResult.innerHTML = 'Result is invalid!';
                        }
                    });               
                }
            }, 3000); 
        }
    </script>

    <script>
        var stateSpan = true;
        var stateButton = true;
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        
        function scanTag() {
            if (screenWidth > 1000) {
                var mainElement = document.getElementById('main-scanner');
                if (document.querySelector('video') == null) {
                    mainElement.style.width = '30vw';
                } else {
                    mainElement.style.width = null;
                }
            } 

            var readerElement = document.getElementById('reader');
            if (readerElement) {
                readerElement.style.border = "none";
            }

            var button = document.getElementById('html5-qrcode-button-camera-start');
            if (button) {
                button.classList.add('btn', 'btn-success');
                button.style.margin = '10px';
            }

            var button = document.getElementById('html5-qrcode-button-camera-stop');
            if (button) {
                button.classList.add('btn', 'btn-danger');
                button.style.margin = '10px';
            }

            var button = document.getElementById('html5-qrcode-button-camera-permission');
            if (button) {
                button.classList.add('btn', 'btn-success');
                button.style.width = '250px';
            }

            var header_msg = document.getElementById('reader__header_message');
            if (header_msg) {
                header_msg.classList.add('font-weight-normal');
                header_msg.classList.add('mx-auto');
                header_msg.style.width = '20vw';
            }

            var videos = document.getElementsByTagName('video');
            for (var i = 0; i < videos.length; i++) {
                videos[i].style.borderRadius = '10px';
            }

            var selectElement = document.getElementById('html5-qrcode-select-camera');
            if (selectElement) {
                selectElement.removeAttribute('disabled');
                selectElement.classList.add('form-select', 'border', 'rounded', 'border-success');
            }
            
            var divElement = document.getElementById('reader__scan_region');
            if (divElement) {
                divElement.style.minHeight = '180px';
                divElement.style.marginBottom = '10px';
            }

            var divElement = document.getElementById('reader__dashboard_section');
            if (divElement) {
                divElement.style.paddingBottom = '20px';
                divElement.style.textAlign = null;
            }

            var imgElement = document.querySelector('img[alt="Camera based scan"]');
            if (imgElement) {
                var iElement = document.createElement('i');
                iElement.className = 'fa-light fa-qrcode text-dark';
                iElement.style.transform = 'scale(7)';
                iElement.style.paddingTop = '24px';
                imgElement.replaceWith(iElement);
            }
            
            var scanRegion = document.getElementById("reader__scan_region");
            var brElements = scanRegion.getElementsByTagName("br");
    
            if (brElements.length > 0) {
                for (var i = 0; i < brElements.length; i++) {
                    var brElement = brElements[i];
                    brElement.parentNode.removeChild(brElement);
                }
            }

            var region = document.getElementById('qr-shaded-region');
            if (region) {region.style.borderRadius = '10px';}

            var tag = document.querySelector('img[alt="Info icon"]');
            if (tag) {tag.remove();}

            var spanToHide = document.getElementById('html5-qrcode-anchor-scan-type-change');
            if (spanToHide) {spanToHide.style.display = "none";} 

            var selectElement = document.getElementById('html5-qrcode-select-camera');
            if (selectElement && stateSpan) {
                const lineBreak = document.createElement('br');
                selectElement.parentNode.insertBefore(lineBreak, selectElement);
                stateSpan = false;
                }

            var selectElement = document.getElementById('html5-qrcode-button-camera-start');
            if (selectElement && stateButton) {
                const lineBreak = document.createElement('br');
                selectElement.parentNode.insertBefore(lineBreak, selectElement);
                stateButton = false;
                }

            var spans = document.querySelectorAll('span');
            spans.forEach(function(span) {
                if (span.innerHTML.includes('Select Camera (2)')) {
                    span.style.marginRight = null;
                    span.classList.add('text-dark', 'font-weight-bold');
                }
            });
        }

        setInterval(scanTag, 50);
    </script>
@endpush