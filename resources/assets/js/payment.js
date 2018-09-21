window.$ = window.jQuery = require('jquery');
let moment = require('moment');

require('moment-duration-format');

function getTimeLeft(date) {
	return moment.duration(moment(date).diff(moment(), 'seconds'), 'seconds').format('d[d] h[h] m[m] s[s]')
}

$(function () {
	let inputs         = $('.input'),
		personalData   = $('#personal-data'),
		toggleCheckbox = personalData.find('#personal-data-invoice-toggle'),
		invoiceForm    = personalData.find('#personal-data-invoice-form'),
		countdown      = $('.signups-countdown'),
		theDate        = countdown.data('start'), // ;)
		firstNameInput = $('#first_name'),
		lastNameInput = $('#last_name'),
		recipientInput = $('#recipient')

	lastNameInput.on('change', () => {
		if (recipientInput.val() === '') {
			recipientInput.val(`${firstNameInput.val()} ${lastNameInput.val()}`)
		}
	});

	window.setInterval(function() {
		countdown.html(getTimeLeft(theDate));
	}, 1000);

	toggleCheckbox.on('change', function () {
		invoiceForm.toggleClass('show').toggleClass('hidden');
	});

	inputs.on('focus', function (event) {
		$(event.target).siblings('.text-danger').remove();
	});

	$('button.p24-submit').click(function () {
        let formId = $(this).data('id');
        let payment = $(this).data('payment');
        $(this).addClass('is-loading');

		$.ajax({
			data: {
				controller: 'PaymentAjaxController',
				method: 'setPaymentMethod',
				payment: payment,
				sess_id: $('[name="p24_session_id"]').val()
			},
			success: function (response) {
				if (response.status === 'success') {
					$(`#${formId}`).submit();
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
