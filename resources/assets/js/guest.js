window.$ = window.jQuery = require('jquery');

$(function() {
	var $navToggle = $('.nav-toggle'),
		$navMenu = $('.nav-menu'),
		$logoutLink = $navMenu.find('.logout-link'),
		$logoutForm = $navMenu.find('#logout-form'),
		$form = $('form'),
		$submit = $form.find('.button:submit')

	$navToggle.click((event) => {
		$navToggle.toggleClass('is-active')
		$navMenu.toggleClass('is-active')
	})

	$form.on('submit', (event) => {
		$submit.addClass('is-loading')
	})

	if ($logoutLink.length) {
		$logoutLink.click((event) => {
			$logoutForm.submit()
		})
	}
})
