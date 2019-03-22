import $ from 'jquery';
import moment from 'moment';
import 'moment/locale/pl';
import 'moment-duration-format';

window.$ = window.jQuery = $;

function formValidation() {
	$('.input').on('focus', function (event) {
		$(event.target).siblings('.text-danger').remove();
	});
}

function personalDataForm() {
	const firstNameInput = $('#first_name');
	const lastNameInput = $('#last_name');
	const recipientInput = $('#recipient');

	lastNameInput.on('change', () => {
		if (recipientInput.val() === '') {
			recipientInput.val(`${firstNameInput.val()} ${lastNameInput.val()}`);
		}
	});
}

function invoiceForm() {
	const toggleCheckbox = $('#invoice');
	const form = $('#personal-data-invoice-form');


	if (toggleCheckbox.length && toggleCheckbox[0].checked) {
		form.addClass('show').removeClass('hidden');
	}

	toggleCheckbox.on('change', function () {
		form.toggleClass('show').toggleClass('hidden');
	});
}

function signupsClosed() {
	const countdown = $('.signups-countdown');
	const theDate = countdown.data('start');
	const getTimeLeft = (date) => moment.duration(
		moment(date * 1000).diff(moment(), 'seconds'), 'seconds'
	).format('d[d] h[h] m[m] s[s]');

	window.setInterval(function () {
		countdown.html(getTimeLeft(theDate));
	}, 1000);
}

function modal($modal, $opener, $closer) {
	$opener.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$modal.addClass('is-active');
	});

	$closer.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$modal.removeClass('is-active');
	});
}

function personalIdentityNumberModal() {
	modal($('#personal-identity-number-modal'), $('#personal-identity-number-modal-opener'), $('#personal-identity-number-modal-closer'));
}

function instalmentsModal() {
	modal($('#instalments-modal'), $('#instalments-modal-opener'), $('#instalments-modal-closer'));
}

function loginModal() {
	modal($('#login-modal'), $('#login-modal-opener'), $('#login-modal-closer'));
}

function cart() {
	const cartClose = document.getElementById('cartClose');
	const cartIcon = document.getElementById('cartIcon');
	const cartBox = document.getElementById('cartBox');

	cartClose && cartClose.addEventListener('click', () => {
		cartBox.classList.add('-dNone');
	});

	cartIcon && cartIcon.addEventListener('click', () => {
		cartBox.classList.toggle('-dNone');
	});
}

function identityNumber() {
	const noIdentityNumberCheckbox = document.getElementById('no_identity_number');
	const passportNumberGroup = document.getElementById('passportNumberGroup');
	const personalIdentityNumberGroup = document.getElementById('personalIdentityNumberGroup');

	const toggleIdentityNumber = showIdentityNumber => {
		if (showIdentityNumber) {
			passportNumberGroup.classList.add('-dNone');
			passportNumberGroup.querySelector('input').value = '';
			personalIdentityNumberGroup.classList.remove('-dNone');
		} else {
			passportNumberGroup.classList.remove('-dNone');
			personalIdentityNumberGroup.classList.add('-dNone');
			personalIdentityNumberGroup.querySelector('input').value = '';
		}
	};

	if (noIdentityNumberCheckbox) {
		noIdentityNumberCheckbox.addEventListener('click', e => toggleIdentityNumber(!e.target.checked));
	}
}

function logoutLinks() {
	const logoutLinks = document.getElementsByClassName('logout-link');
	const logoutForm = document.getElementById('logoutForm');

	for (let i = 0; i < logoutLinks.length; i++) {
		logoutLinks[i].addEventListener('click', () => logoutForm && logoutForm.submit());
	}
}

function accountDropdown() {
	const accountDropdown = document.getElementById('accountDropdown');
	const accountDropdownTrigger = document.getElementById('accountDropdownTrigger');

	accountDropdownTrigger && accountDropdownTrigger.addEventListener('click', () => {
		accountDropdown && accountDropdown.classList.toggle('-active');
	});

	const clickHandler = ({target}) => {
		if (!accountDropdown) return;
		if (accountDropdown.classList.contains('-active') && !accountDropdown.contains(target)) {
			accountDropdown.classList.remove('-active');
		}
	};

	document.addEventListener('click', clickHandler);
}

function passwordVisibilityToggle() {
	const element = document.getElementById('passwordVisibilityToggle');

	element && element.addEventListener('click', (e) => {
		const toggle = e.target;
		const input = e.target.parentNode.querySelector('input');

		if (input.type === 'password') {
			input.type = 'text';
			toggle.classList.add('fa-eye-slash');
			toggle.classList.remove('fa-eye');
		} else {
			input.type = 'password';
			toggle.classList.remove('fa-eye-slash');
			toggle.classList.add('fa-eye');
		}
		input.focus();
	});
}

function formatDates() {
	$('[data-date-format]').each(function () {
		const $this = $(this);
		const date = moment.unix($this.attr('data-timestamp'));
		const format = $this.attr('data-date-format');

		$this.html(date.format(format));
	});
}

function confirmOrderForm() {
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

	$.ajaxSetup({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: $('body').data('base') + '/ax',
		data: {},
		method: 'POST',
		error: function (error) {
			console.log(error);
		}
	});
}

formValidation();
personalDataForm();
invoiceForm();
signupsClosed();
personalIdentityNumberModal();
instalmentsModal();
loginModal();
cart();
identityNumber();
logoutLinks();
accountDropdown();
passwordVisibilityToggle();
formatDates();
confirmOrderForm();
