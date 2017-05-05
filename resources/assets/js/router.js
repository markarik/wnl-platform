import Vue from 'vue'
import Router from 'vue-router'
import { scrollToTop } from 'js/utils/animations'
import { resource } from 'js/utils/config'
import { isProduction } from 'js/utils/env'

Vue.use(Router)

let routes = []

if (isProduction()) {
	routes = [
		{
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
			redirect: '/app/myself/countdown',
		},
		{
			name: 'logout',
			path: '/logout',
			beforeEnter: () => {
				document.getElementById('logout-form').submit()
			}
		},
		{
			path: '*',
			redirect: '/app/myself/countdown',
		},
	]
} else {
	routes = [
		{
			path: '/app/courses/:courseId',
			component: require('js/components/course/Course.vue'),
			props: true,
			children: [
				{
					name: resource('courses'),
					path: '',
					component: require('js/components/course/Overview.vue'),
					props: true,
				},
				{
					name: resource('lessons'),
					path: '/app/courses/:courseId/lessons/:lessonId',
					component: require('js/components/course/Lesson.vue'),
					props: true,
					children: [
						{
							name: resource('screens'),
							path: 'screens/:screenId/:slide?',
							component: require('js/components/course/Screen.vue'),
							props: true,
						}
					]
				}
			]
		},
		{
			name: 'myself',
			path: '/app/myself',
			component: require('js/components/user/Myself.vue'),
			props: true,
			children: [
				{
					name: 'my-orders',
					path: 'orders',
					component: require('js/components/user/MyOrders.vue')
				}
			]
		},
		{
			name: 'dashboard',
			path: '/app',
			redirect: { name: 'courses', params: { courseId: 1 } }
		},
		{
			name: 'logout',
			path: '/logout',
			beforeEnter: () => {
				document.getElementById('logout-form').submit()
			}
		},
		{
			path: '*',
			redirect: '/app/'
		}
	]
}

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => {
		scrollToTop()
		return {x: 0, y:0}
	},
	routes
})
