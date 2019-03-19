import $ from 'jquery';
window.$ = window.jQuery = $;

$(window).on('unload', function(){});

$(function() {
	const navToggle = $('.nav-toggle');
	const navMenu = $('.nav-menu');
	const form = $('form');
	const buttons = $('.button');
	const modalsContainer = $('.modals');
	const loginModalButton = $('.opens-login-modal');
	const loginModal = modalsContainer.find('#login-modal');
	const loginCloseModalButton = loginModal.find('.delete');
	const expandable = $('.expandable');
	const logoutLink = document.getElementById('logoutLink');
	const logoutForm = document.getElementById('logoutForm');
	const accountDropdown = document.getElementById('accountDropdown');
	const accountDropdownTrigger = document.getElementById('accountDropdownTrigger');

	buttons.removeClass('is-disabled').removeClass('is-loading');

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
