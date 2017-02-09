window.$ = window.jQuery = require('jquery');

var $personalData = $('#personal-data'),
	$toggleCheckbox = $personalData.find('#personal-data-invoice-toggle'),
	$invoiceForm = $personalData.find('#personal-data-invoice-form'),
	$touContainer = $('.tou'),
	$touModal = $touContainer.find('.modal'),
	$touOpenModalLink = $touContainer.find('#tou-open-modal-link'),
	$touCloseModalButton = $touModal.find('.delete');

$toggleCheckbox.on('change', function () {
	$invoiceForm.toggleClass('show').toggleClass('hidden');
});

$touOpenModalLink.on('click', function (event) {
	event.preventDefault();
	$touModal.addClass('is-active');
});

$touCloseModalButton.on('click', function (event) {
	event.preventDefault();
	$touModal.removeClass('is-active');
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

$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	url: $('body').data('base') + '/ax',
	data: {},
	method: 'POST',
	error: function (error) {
		console.log(error);
	}
});