import $ from 'jquery';
window.$ = window.jQuery = $;

$(window).on('unload', function(){});

$(function() {
	var navToggle = $('.nav-toggle'),
		navMenu = $('.nav-menu'),
		form = $('form'),
		buttons = $('.button'),
		modalsContainer = $('.modals'),
		loginModalButton = $('.opens-login-modal'),
		loginModal = modalsContainer.find('#login-modal'),
		loginCloseModalButton = loginModal.find('.delete'),
		expandable = $('.expandable');

	buttons.removeClass('is-disabled').removeClass('is-loading');

	const logoutLink = document.getElementById('logoutLink');
	const logoutForm = document.getElementById('logoutForm');
	const accountDropdown = document.getElementById('accountDropdown');
	const accountDropdownTrigger = document.getElementById('accountDropdownTrigger');

	navToggle.click((event) => {
		navToggle.toggleClass('is-active');
		navMenu.toggleClass('is-active');
	});

	loginModalButton.on('mousedown touchstart', function (event) {
		event.preventDefault();
		loginModal.addClass('is-active');
	});

	loginCloseModalButton.on('mousedown touchstart', function (event) {
		event.preventDefault();
		loginModal.removeClass('is-active');
	});

	form.on('submit', (event) => {
		buttons.addClass('is-disabled');
		$(event.target).find('.button:submit').removeClass('is-disabled').addClass('is-loading');
	});

	if (expandable.length > 0) {
		expandable.find('.expand').on('mousedown touchstart', function (event) {
			expandable.find('.expandable-content').slideDown();
		});
	}

	logoutLink && logoutLink.addEventListener('click', () => {
		logoutForm && logoutForm.submit();
	});

	accountDropdownTrigger && accountDropdownTrigger.addEventListener('click', () => {
		accountDropdown && accountDropdown.classList.toggle('-active');
	});

	const clickHandler = ({target}) => {
		if (accountDropdown.classList.contains('-active') && !accountDropdown.contains(target)) {
			accountDropdown.classList.remove('-active');
		}
	};

	document.addEventListener('click', clickHandler);
});
