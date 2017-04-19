require('./bootstrap');
import Vue from 'vue'

// Sync vue-router and vuex
import {sync} from 'vuex-router-sync'
import store from 'js/store/store'
import router from 'js/router'
sync(store, router)

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
const wnlog = new Logger()

// Set up App
wnlog.emergency('EM: Starting application...')
wnlog.alert('AL: Starting application...')
wnlog.critical('CR: Starting application...')
wnlog.error('ER: Starting application...')
wnlog.warning('WAR: Starting application...')
wnlog.notice('NOT: Starting application...')
wnlog.info('INFO: Starting application...')
wnlog.debug('DEB: Starting application...')

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
