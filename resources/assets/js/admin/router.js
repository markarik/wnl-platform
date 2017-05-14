import Vue from 'vue'
import Router from 'vue-router'
import {scrollToTop} from 'js/utils/animations'
import {resource} from 'js/utils/config'
import {isProduction} from 'js/utils/env'

Vue.use(Router)

let routes = [
	{
		// Temporarily duplicated from main app routes,
		name: 'lessons',
		path: '/admin/app/lessons/:lessonId?',
		component: require('js/admin/components/lessons/Lessons.vue'),
		props: true,
		children: [
			{
				name: 'screen-edit',
				path: 'screens/:screenId?',
				component: require('js/admin/components/lessons/edit/ScreensEditor.vue')
			},
		]
	},
	{
		name: 'dashboard',
		path: '/admin/app',
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
		return {x: 0, y: 0}
	},
	routes
})
