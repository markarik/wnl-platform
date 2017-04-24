require('./bootstrap');
import Vue from 'vue'

// Sync vue-router and vuex
import {sync} from 'vuex-router-sync'
import store from 'js/store/store'
import router from 'js/router'
sync(store, router)

// Import plugins
import VueSweetAlert from 'vue-sweetalert'
Vue.use(VueSweetAlert)

// Import and register global components
import Avatar from 'js/components/global/Avatar.vue'
import Emoji from 'js/components/global/Emoji.vue'
import Icon from 'js/components/global/Icon.vue'
import ImageButton from 'js/components/global/ImageButton.vue'
import TextLoader from 'js/components/global/TextLoader.vue'
Vue.component('wnl-avatar', Avatar)
Vue.component('wnl-emoji', Emoji)
Vue.component('wnl-icon', Icon)
Vue.component('wnl-image-button', ImageButton)
Vue.component('wnl-text-loader', TextLoader)

// Setup a logger
import Logger from 'js/utils/logger'
$wnl.logger = new Logger()

// Set up App
$wnl.logger.debug('Starting application...')

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
		$wnl.logger.error(error)
	}
});
