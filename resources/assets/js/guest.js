import $ from 'jquery';
window.$ = window.jQuery = $;

$(window).on('unload', function(){});

$(function() {
	const navToggle = $('.nav-toggle');
	const navMenu = $('.nav-menu');
	const form = $('form');
	const buttons = $('.button');

	buttons.removeClass('is-disabled').removeClass('is-loading');

	navToggle.click(() => {
		navToggle.toggleClass('is-active');
		navMenu.toggleClass('is-active');
	});

	form.on('submit', (event) => {
		buttons.addClass('is-disabled');
		$(event.target).find('.button:submit').removeClass('is-disabled').addClass('is-loading');
	});
});
