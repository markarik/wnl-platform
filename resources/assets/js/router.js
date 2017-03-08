import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => ({ y: 0 }),
	routes: [
		{
			name: 'courses',
			path: '/app/courses/:courseId',
			component: require('./components/Course.vue'),
			props: true
		},
		{
			name: 'lessons',
			path: '/app/courses/:courseId/lessons/:lessonId',
			component: require('./components/Lesson.vue'),
			props: true,
			children: [
				{
					name: 'screens',
					path: 'screens/:screenId/:slide?',
					component: require('./components/Screen.vue'),
					props: true,
				}
			]
		},
		{
			name: 'myself',
			path: '/app/myself',
			component: require('./components/user/Myself.vue'),
			props: true,
			children: [
				{
					name: 'my-orders',
					path: 'orders',
					component: require('./components/user/MyOrders.vue')
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
