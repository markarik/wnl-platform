window.$ = window.jQuery = require('jquery');

$(function() {
	var $navToggle = $('.nav-toggle'),
		$navMenu = $('.nav-menu'),
		$logoutLink = $navMenu.find('.logout-link'),
		$logoutForm = $navMenu.find('#logout-form'),
		$form = $('form'),
		$submit = $form.find('.button:submit'),
		$modalsContainer = $('.modals'),
		$touModal = $modalsContainer.find('#tou-modal'),
		$touOpenModalLinks = $('.tou-open-modal-link'),
		$touCloseModalButton = $touModal.find('.delete'),
		$privacyModal = $modalsContainer.find('#privacy-policy-modal'),
		$privacyOpenModalLinks = $('.privacy-policy-open-modal-link'),
		$privacyCloseModalButton = $privacyModal.find('.delete'),
		$expandable = $('.expandable')

	$navToggle.click((event) => {
		$navToggle.toggleClass('is-active')
		$navMenu.toggleClass('is-active')
	})

	$touOpenModalLinks.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$touModal.addClass('is-active');
	})

	$touCloseModalButton.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$touModal.removeClass('is-active');
	})

	$privacyOpenModalLinks.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$privacyModal.addClass('is-active');
	})

	$privacyCloseModalButton.on('mousedown touchstart', function (event) {
		event.preventDefault();
		$privacyModal.removeClass('is-active');
	})

	$form.on('submit', (event) => {
		$submit.addClass('is-loading')
	})

	if ($expandable.length > 0) {
		$expandable.find('.expand').on('mousedown touchstart', function (event) {
			$expandable.find('.expandable-content').slideDown()
		})
	}

	if ($logoutLink.length) {
		$logoutLink.click((event) => {
			$logoutForm.submit()
		})
	}
})
