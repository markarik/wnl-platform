require('js/bootstrap');
import Vue from 'vue'

// Sync vue-router and vuex
import {sync} from 'vuex-router-sync'
import store from 'js/store/store'
import router from 'js/admin/router'
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

import Admin from 'js/admin/components/Admin.vue'
const admin = new Vue({
	router,
	store,
	...Admin
}).$mount('#admin')
