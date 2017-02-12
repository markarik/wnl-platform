require('./bootstrap');
import Vue from 'vue'
import App from './components/App.vue'
import store from './store/store'

const currentView = $('#root').data('view')

new Vue({
	store,
	el: '#root',
	render: h => h(App),
	created: () => {
		store.dispatch('setCurrentView', currentView)
		store.dispatch('setCurrentUser')
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