require('./bootstrap');
import Vue from 'vue'

// Sync vue-router and vuex
import {sync} from 'vuex-router-sync'
import store from 'js/store/store'
import router from 'js/router'
sync(store, router)

// Import and register global components
import Avatar from 'js/components/global/Avatar.vue'
import ImageButton from 'js/components/global/ImageButton.vue'
import Icon from 'js/components/global/Icon.vue'
import Emoji from 'js/components/global/Emoji.vue'
Vue.component('wnl-avatar', Avatar)
Vue.component('wnl-image-button', ImageButton)
Vue.component('wnl-icon', Icon)
Vue.component('wnl-emoji', Emoji)

// Setup a debug function
import { isDebug } from 'js/utils/env'
$wnl.debug = (data) => {
	if (isDebug()) console.log(data)
}
if (isDebug()) {
	Vue.config.performance = true
}

// Set up App
import App from 'js/components/App.vue'
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
