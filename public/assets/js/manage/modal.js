"use strict";

$("#scanner-modal").fireModal({
    title: 'Scanner Page Instructions',
    body: '\
        <ul>\
            <li>Row 1</li>\
            <li>Row 2</li>\
            <li>Row 3</li>\
        </ul>', 
    center: true,
    buttons: [
        {
            text: 'Continue',
            class: 'btn btn-warning btn-shadow text-white',
            handler: function(modal) {
                window.location.href = "/scan/scanner";
            }
        }
    ]
});

$("#submit-modal").fireModal({
    title: 'Configuring Station',
    body: '\
        <div class="text-center pt-2">\
            <div id="icon-submit">\
                <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem;" role="status"></div>\
            </div>\
            <h6 id="feedback-form" class="font-weight-normal">Connecting to Station</h6>\
            <a id="retry-button" class="btn btn-success text-white mt-3" style="display: none;">Retry</a>\
        </div>', 
    center: true
});