import $ from 'jquery';
window.$ = window.jQuery = $;

$(window).on('unload', function(){});

$(function() {
	var navToggle = $('.nav-toggle'),
		navMenu = $('.nav-menu'),
		logoutLink = navMenu.find('.logout-link'),
		logoutForm = navMenu.find('#logout-form'),
		form = $('form'),
		buttons = $('.button'),
		modalsContainer = $('.modals'),
		loginModalButton = $('.opens-login-modal'),
		loginModal = modalsContainer.find('#login-modal'),
		loginCloseModalButton = loginModal.find('.delete'),
		expandable = $('.expandable');

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

	if (logoutLink.length) {
		logoutLink.click((event) => {
			logoutForm.submit();
		});
	}
});
