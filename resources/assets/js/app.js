require('./bootstrap');
import Vue from 'vue'
import App from './components/App.vue'
import { sync } from 'vuex-router-sync'
import store from './store/store'
import router from './router'

// TODO: Move it to a config/utils file
global.$fn = {
	getApiUrl: function (path) {
		return '/papi/v1/' + path
	},
	getUrl: function (path) {
		return $wnl.baseURL+ '/' + path
	}
}

sync(store, router)

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

function checkOrderPaymentStatus(orderId){
	(function sendRequest() {
		$.ajax({
			data: {
				controller: 'PaymentAjaxController',
				method: 'checkOrderPaymentStatus',
				orderId: orderId
			},
			success: function (response){
				if (response.orderPaid){
					$('#loader-'+orderId).parent().html('Zapłacono');
					$('#change-method-button-'+orderId).hide();
				} else {
					setTimeout(sendRequest, 10000);
				}
			}
		});
	})();
}

$(document).ready(function () {
	$('.order-pending-notification').each(function(element){
		checkOrderPaymentStatus($(this).data('id'));
	});
});
