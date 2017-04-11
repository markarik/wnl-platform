window.$ = window.jQuery = require('jquery');

$(function() {
	var $navToggle = $('.nav-toggle'),
		$navMenu = $('.nav-menu'),
		$logoutLink = $navMenu.find('.logout-link'),
		$logoutForm = $navMenu.find('#logout-form')

	$navToggle.click((event) => {
		console.log('dupa')
		$navToggle.toggleClass('is-active')
		$navMenu.toggleClass('is-active')
	})

	if ($logoutLink.length) {
		$logoutLink.click((event) => {
			$logoutForm.submit()
		})
	}
})
