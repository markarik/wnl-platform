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
			name: 'Dashboard',
			path: '/app',
			component: require('./components/Dashboard.vue'),
		},
		{
			path: '*',
			redirect: '/app/'
		}
	]
})
