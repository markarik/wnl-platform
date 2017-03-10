import Vue from 'vue'
import Router from 'vue-router'
import { routes } from 'js/utils/constants'

Vue.use(Router)

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => ({ y: 0 }),
	routes: [
		{
			path: '/app/courses/:courseId',
			component: require('js/components/course/Course.vue'),
			props: true,
			children: [
				{
					name: routes.courses,
					path: '',
					component: require('js/components/course/Overview.vue'),
					props: true,
				},
				{
					name: routes.lessons,
					path: '/app/courses/:courseId/lessons/:lessonId',
					component: require('js/components/course/Lesson.vue'),
					props: true,
					children: [
						{
							name: routes.screens,
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
			name: 'Dashboard',
			path: '/app',
			redirect: { name: 'courses', params: { courseId: 1 } }
		},
		{
			path: '*',
			redirect: '/app/'
		}
	]
})
