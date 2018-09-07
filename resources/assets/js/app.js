require('./bootstrap');
import Vue from "vue";
import {sync} from "vuex-router-sync";
import store from "js/store/store";
import router from "js/router";
import Quill from "quill";
import MentionBlot from "js/classes/mentionblot";
import VueI18n from "vue-i18n";
import {pl} from "js/i18n";
import VueSweetAlert from "vue-sweetalert";
import VueSimpleBreakpoints from "vue-simple-breakpoints";
import VueKindergarten from "vue-kindergarten";
import Alert from "js/components/global/Alert.vue";
import Avatar from "js/components/global/Avatar.vue";
import Emoji from "js/components/global/Emoji.vue";
import Icon from "js/components/global/Icon.vue";
import ImageButton from "js/components/global/ImageButton.vue";
import TextLoader from "js/components/global/TextLoader.vue";
import Upload from "js/components/global/Upload.vue";
import Logger from "js/utils/logger";
import App from "js/components/App.vue";
import WnlSocket from "js/plugins/socket";
import WnlAxios from "js/plugins/axios";

// Sync vue-router and vuex
sync(store, router)


// Import plugins
// i18n

Vue.use(VueI18n)
Vue.config.lang = 'pl'

const messages = {pl}
const i18n = new VueI18n({fallbackLocal: 'pl', locale: 'pl', messages})

// SweetAlert2
Vue.use(VueSweetAlert)
Vue.use(WnlSocket, {store})
Vue.use(WnlAxios, {store, router})

// Simple Breakpoints
Vue.use(VueSimpleBreakpoints, {
	mobile: 759, //mobile needs a top boundary, not a bottom one
	tablet: 760,
	small_desktop: 980,
	large_desktop: 1280
})

// Kindergarten
Vue.use(VueKindergarten, {
	child: (store) => {
		return store && store.getters.currentUser
	}
})

// Import and register global components

Vue.component('wnl-alert', Alert)
Vue.component('wnl-avatar', Avatar)
Vue.component('wnl-emoji', Emoji)
Vue.component('wnl-icon', Icon)
Vue.component('wnl-image-button', ImageButton)
Vue.component('wnl-text-loader', TextLoader)
Vue.component('wnl-upload', Upload)

// Setup a logger
$wnl.logger = new Logger()

// Set up App
$wnl.logger.debug('Starting application...')

const app = new Vue({
	router,
	store,
	i18n,
	...App
}).$mount('#app')

Quill.register({
  'formats/mention': MentionBlot
});

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
