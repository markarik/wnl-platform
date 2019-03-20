import $ from 'jquery';
import moment from 'moment';
import 'moment/locale/pl';
import 'moment-duration-format';
// FIXME remove before merging to master
import 'sass/app.scss';

window.$ = window.jQuery = $;

function getTimeLeft(date) {
	return moment.duration(moment(date * 1000).diff(moment(), 'seconds'), 'seconds').format('d[d] h[h] m[m] s[s]');
}

$(function () {
	const inputs = $('.input');
	const toggleCheckbox = $('#invoice');
	const invoiceForm = $('#personal-data-invoice-form');
	const countdown = $('.signups-countdown');
	const theDate = countdown.data('start');
	const firstNameInput = $('#first_name');
	const lastNameInput = $('#last_name');
	const recipientInput = $('#recipient');
	const personalIdentityNumberModal = $('#personal-identity-number-modal');
	const instalmentsModal = $('#instalments-modal');
	const cartClose = document.getElementById('cartClose');
	const cartIcon = document.getElementById('cartIcon');
	const cartBox = document.getElementById('cartBox');
	const noIdentityNumberCheckbox = document.getElementById('no_identity_number');
	const passportNumberGroup = document.getElementById('passportNumberGroup');
	const personalIdentityNumberGroup = document.getElementById('personalIdentityNumberGroup');

	if (toggleCheckbox.length && toggleCheckbox[0].checked) {
		invoiceForm.addClass('show').removeClass('hidden');
	}

	const toggleIdentityNumber = showIdentityNumber => {
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

	window.setInterval(function () {
		countdown.html(getTimeLeft(theDate));
	}, 1000);

	toggleCheckbox.on('change', function () {
		invoiceForm.toggleClass('show').toggleClass('hidden');
	});

	inputs.on('focus', function (event) {
		$(event.target).siblings('.text-danger').remove();
	});


	// Confirm order
	const paymentOptions = $('.m-paymentOption');
	paymentOptions.click(function () {
		paymentOptions.removeClass('-active');
		$(this)
			.addClass('-active')
			.find('input').prop('checked', true);
	});


	$('#confirmOrderSubmit').click(function () {
		const isFreePayment = $('input[name=payment_method]').length === 0;
		const paymentMethod = $('input[name=payment_method]:checked').val();
		const instalmentsSelected = $('#instalments:checked').length > 0;

		let customPaymentMethod;
		if (isFreePayment) {
			customPaymentMethod = 'free';
		} else if (instalmentsSelected) {
			customPaymentMethod = 'instalments';
		} else {
			customPaymentMethod = 'online';
		}

		$(this).addClass('-loading');
		if (paymentMethod === 'now') {
			$.ajax({
				data: {
					controller: 'PaymentAjaxController',
					method: 'setPaymentMethod',
					payment: customPaymentMethod,
					sess_id: $('[name="p24_session_id"]').val()
				},
				success: function (response) {
					if (response.status === 'success') {
						$(instalmentsSelected ? '#instalmentsP24Form' : '#fullPaymentP24Form').submit();
					}
				}
			});
		} else {
			$('#customPaymentMethodInput').val(customPaymentMethod);
			$('#customPaymentForm').submit();
		}
	});

	$('#personal-identity-number-modal-opener').on('mousedown touchstart', function (event) {
		event.preventDefault();
		personalIdentityNumberModal.addClass('is-active');
	});

	$('#personal-identity-number-modal-closer').on('mousedown touchstart', function (event) {
		event.preventDefault();
		personalIdentityNumberModal.removeClass('is-active');
	});

	$('#instalments-modal-opener').on('mousedown touchstart', function (event) {
		event.preventDefault();
		instalmentsModal.addClass('is-active');
	});

	$('#instalments-modal-closer').on('mousedown touchstart', function (event) {
		event.preventDefault();
		instalmentsModal.removeClass('is-active');
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
