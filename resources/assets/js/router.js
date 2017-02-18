import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => ({ y: 0 }),
	routes: [
		{
			name: 'Dashboard',
			path: '/app/',
			component: require('./components/Course.vue'),
			props: { id: 1 }
		},
		{
			path: '*',
			redirect: '/app/'
		}
	]
})
