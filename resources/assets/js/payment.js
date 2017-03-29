window.$ = window.jQuery = require('jquery');

$(function() {
	var $inputs = $('.input'),
		$personalData = $('#personal-data'),
		$toggleCheckbox = $personalData.find('#personal-data-invoice-toggle'),
		$invoiceForm = $personalData.find('#personal-data-invoice-form'),
		$modalsContainer = $('.modals'),
		$touModal = $modalsContainer.find('#tou-modal'),
		$touOpenModalLinks = $('.tou-open-modal-link'),
		$touCloseModalButton = $touModal.find('.delete'),
		$privacyModal = $modalsContainer.find('#privacy-policy-modal'),
		$privacyOpenModalLinks = $('.privacy-policy-open-modal-link'),
		$privacyCloseModalButton = $privacyModal.find('.delete');

	$toggleCheckbox.on('change', function () {
		$invoiceForm.toggleClass('show').toggleClass('hidden');
	});

	$touOpenModalLinks.on('click', function (event) {
		event.preventDefault();
		$touModal.addClass('is-active');
	});

	$touCloseModalButton.on('click', function (event) {
		event.preventDefault();
		$touModal.removeClass('is-active');
	});

	$privacyOpenModalLinks.on('click', function (event) {
		event.preventDefault();
		$privacyModal.addClass('is-active');
	});

	$privacyCloseModalButton.on('click', function (event) {
		event.preventDefault();
		$privacyModal.removeClass('is-active');
	});

	$inputs.on('focus', function (event) {
		$(event.target).siblings('.text-danger').remove();
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
});
