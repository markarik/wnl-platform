// TODO: Move it to a config/utils file
global.$fn = {
	getEnv() {
		return $wnl.env
	},
	isDevEnv() {
		return $wnl.env === 'dev'
	},
	getApiUrl(path) {
		return '/papi/v1/' + path
	},
	getUrl(path) {
		return $wnl.baseURL + '/' + path
	},
	getImageUrl(filename) {
		return $wnl.baseURL + '/images/' + filename
	},
	getInitial(text) {
		return text.charAt(0).toUpperCase()
	},
	getInitials(username) {
		let hasSpaceRegex = /\s/
		if (username.length === 1) {
			return username.toUpperCase()
		} else if (hasSpaceRegex.test(username)) {
			let split = username.split(' ')
			return global.$fn.getInitial(split[0]) + global.$fn.getInitial(split[1])
		} else {
			return username.slice(0, 2).toUpperCase()
		}
	}
}

require('./bootstrap');
import Vue from 'vue'

// Sync vue-router and vuex
import {sync} from 'vuex-router-sync'
import store from './store/store'
import router from './router'
sync(store, router)

// Import and register global components
import Avatar from './components/global/Avatar.vue'
import ImageButton from './components/global/ImageButton.vue'
import Icon from './components/global/Icon.vue'
Vue.component('wnl-avatar', Avatar)
Vue.component('wnl-image-button', ImageButton)
Vue.component('wnl-icon', Icon)

// Set up App
import App from './components/App.vue'
const app = new Vue({
	router,
	store,
	...App
}).$mount('#app')

// TODO: Move it to a separate component
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	url: $('body').data('base') + '/ax',
	data: {},
	method: 'POST',
	error: function (error) {
		console.log(error);
	}
});

function checkOrderPaymentStatus(orderId) {
	(function sendRequest() {
		$.ajax({
			data: {
				controller: 'PaymentAjaxController',
				method: 'checkOrderPaymentStatus',
				orderId: orderId
			},
			success: function (response) {
				if (response.orderPaid) {
					$('#loader-' + orderId).parent().html('Zapłacono');
					$('#change-method-button-' + orderId).hide();
				} else {
					setTimeout(sendRequest, 10000);
				}
			}
		});
	})();
}

$(document).ready(function () {
	$('.order-pending-notification').each(function (element) {
		checkOrderPaymentStatus($(this).data('id'));
	});
});
