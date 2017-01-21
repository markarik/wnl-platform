/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app',

	data: {
		invoice: false
	}
});

$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: $('body').data('base') + '/ax',
    data: {},
    method: 'POST',
    error: function (error) {
        console.log(error);
    }
});

$('button.p24_submit').click(function () {
    $.ajax({
        data: {
            controller: 'PaymentAjaxController',
            method: 'setPaymentMethod',
            payment: 'online',
            sess_id: $('[name="p24_session_id"]').val()
        },
        success: function (response) {
            if (response.status == 'success') {
                $('.p24_form').submit();
            }
        }
    });
});
