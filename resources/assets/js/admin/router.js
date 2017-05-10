import Vue from 'vue'
import Router from 'vue-router'
import {scrollToTop} from 'js/utils/animations'
import {resource} from 'js/utils/config'
import {isProduction} from 'js/utils/env'

Vue.use(Router)

let routes = [
	{
		// Temporarily duplicated from main app routes,
		// dropdown items should be moved to vuex.
		name: 'myself',
		path: '/app/myself',
		component: require('js/components/user/Myself.vue'),
		props: true,
		children: [
			{
				name: 'my-orders',
				path: 'orders',
				component: require('js/components/user/MyOrders.vue')
			},
			{
				name: 'countdown',
				path: 'countdown',
				component: require('js/components/global/SplashScreen.vue'),
			}
		]
	},
	{
		name: 'dashboard',
		path: '/app',
	},
	{
		name: 'logout',
		path: '/logout',
		beforeEnter: () => {
			document.getElementById('logout-form').submit()
		}
	},
]

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => {
		scrollToTop()
		return {x: 0, y: 0}
	},
	routes
})
