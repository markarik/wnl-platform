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
			path: '/app/courses/:cid',
			component: require('./components/Course.vue'),
			props: true,
			children: [
				{
					name: 'lessons',
					path: 'lessons/:lid',
					component: require('./components/Lesson.vue'),
					props: true
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
