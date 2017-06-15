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
					name: 'my-profile',
					path: 'profile',
					component: require('js/components/user/MyProfile.vue')
				},
				{
					name: 'my-address',
					path: 'address',
					component: require('js/components/user/MyAddress.vue')
				},
				{
					name: 'my-billing-data',
					path: 'billing',
					component: require('js/components/user/MyBillingData.vue')
				},
				// {
				// 	name: 'my-settings',
				// 	path: 'settings',
				// 	component: require('js/components/user/MySettings.vue')
				// },
				{
					name: 'my-password',
					path: 'password',
					component: require('js/components/user/MyPassword.vue')
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
			redirect: { name: "my-profile" },
			children: [
				{
					name: 'my-orders',
					path: 'orders',
					component: require('js/components/user/MyOrders.vue')
				},
				{
					name: 'my-profile',
					path: 'profile',
					component: require('js/components/user/MyProfile.vue')
				},
				{
					name: 'my-address',
					path: 'address',
					component: require('js/components/user/MyAddress.vue')
				},
				{
					name: 'my-billing-data',
					path: 'billing',
					component: require('js/components/user/MyBillingData.vue')
				},
				{
					name: 'my-settings',
					path: 'settings',
					component: require('js/components/user/MySettings.vue')
				},
				{
					name: 'my-password',
					path: 'password',
					component: require('js/components/user/MyPassword.vue')
				},
			]
		},
		{
			name: 'collections',
			path: '/app/collections',
			component: require('js/components/collections/Collections.vue'),
			props: true,
			// redirect: { name: "my-profile" },
			children: [
				{
					name: 'collection-slide',
					path: 'slides',
					component: require('js/components/collections/SlidesCollection.vue')
				},
				{
					name: 'collection-qna',
					path: 'qna',
					component: require('js/components/collections/QnaCollection.vue')
				},
				{
					name: 'collection-quiz',
					path: 'quiz',
					component: require('js/components/collections/QuizCollection.vue')
				},
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
	scrollBehavior: (to, from, savedPosition) => {
		if (!from.params.hasOwnProperty('slide') ||
			!to.params.hasOwnProperty('slide') ||
			parseInt(from.params.screenId) !== parseInt(to.params.screenId))
		{
			scrollToTop()
			return {x: 0, y:0}
		}
	},
	routes
})
