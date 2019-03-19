import $ from 'jquery';
import moment from 'moment';
import 'moment/locale/pl';
import 'moment-duration-format';

window.$ = window.jQuery = $;

function getTimeLeft(date) {
	return moment.duration(moment(date*1000).diff(moment(), 'seconds'), 'seconds').format('d[d] h[h] m[m] s[s]');
}

$(function () {
	let inputs         = $('.input'),
		toggleCheckbox = $('#personal-data-invoice-toggle'),
		invoiceForm    = $('#personal-data-invoice-form'),
		countdown      = $('.signups-countdown'),
		theDate        = countdown.data('start'), // ;)
		firstNameInput = $('#first_name'),
		lastNameInput = $('#last_name'),
		recipientInput = $('#recipient');

	const cartClose = document.getElementById('cartClose');
	const cartIcon = document.getElementById('cartIcon');
	const cartBox = document.getElementById('cartBox');
	const noIdentityNumberCheckbox = document.getElementById('no_identity_number');
	const passportNumberGroup = document.getElementById('passportNumberGroup');
	const personalIdentityNumberGroup = document.getElementById('personalIdentityNumberGroup');

	if(toggleCheckbox.find('input').length && toggleCheckbox.find('input')[0].checked) {
		invoiceForm.addClass('show').removeClass('hidden');
	}

	const toggleIdentityNumber = showIdentityNumber  => {
		if (showIdentityNumber) {
			passportNumberGroup.classList.add('-dNone');
			personalIdentityNumberGroup.classList.remove('-dNone');
		} else {
			passportNumberGroup.classList.remove('-dNone');
			personalIdentityNumberGroup.classList.add('-dNone');
		}
	};

	if (noIdentityNumberCheckbox) {
		noIdentityNumberCheckbox.addEventListener('click', e => toggleIdentityNumber(!e.target.checked));
		toggleIdentityNumber(!noIdentityNumberCheckbox.checked);
	}

	cartClose && cartClose.addEventListener('click', () => {
		cartBox.classList.add('-dNone');
	});

	cartIcon && cartIcon.addEventListener('click', () => {
		cartBox.classList.toggle('-dNone');
	});

	lastNameInput.on('change', () => {
		if (recipientInput.val() === '') {
			recipientInput.val(`${firstNameInput.val()} ${lastNameInput.val()}`);
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

	$('[data-date-format]').each(function () {
		const $this = $(this);
		const date = moment.unix($this.attr('data-timestamp'));
		const format = $this.attr('data-date-format');

		$this.html(date.format(format));
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
