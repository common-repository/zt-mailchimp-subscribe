;(function($, window, document, undefined) {
    "use strict";

    var SUBSCRIBE_OBJECT = {};

    SUBSCRIBE_OBJECT.init = function () {
        SUBSCRIBE_OBJECT.params = {
            subscribeForm: document.querySelectorAll('.zt-subscribe__form')
        };

        this.submitForm(SUBSCRIBE_OBJECT.params.subscribeForm);
    };

    SUBSCRIBE_OBJECT.submitForm = function(form) {

        form.forEach(function (el) {
            el.addEventListener('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    type: this.getAttribute('method'),
                    url: this.getAttribute('action'),
                    data: {
                        action: 'request_form_subscribe',
                        'request': $(this).serialize()
                    },
                    success: function (response) {
                        document.querySelector('.zt-subscribe__message').innerHTML = response;
                        el.querySelector('input[name="email_address"]').value = '';
                    },
                    error: function () {
                        document.querySelector('.zt-subscribe__message').innerHTML = 'Something went wrong, try again!';
                    }
                });

                return false;
            });
        });
    };

    document.addEventListener("DOMContentLoaded", function() {
        SUBSCRIBE_OBJECT.init();
    });


})(jQuery, window, document);



