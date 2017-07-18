import Vue from 'vue'
import Router from 'vue-router'
import {scrollToTop} from 'js/utils/animations'
import {resource} from 'js/utils/config'
import {isProduction} from 'js/utils/env'

Vue.use(Router)

let routes = [
	{
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
		name: 'slides',
		path: '/admin/app/slides/edit/:lessonId?/:screenId?',
		component: require('js/admin/components/slides/SlidesEditor.vue'),
	},
	{
		name: 'charts',
		path: '/admin/app/charts',
		component: require('js/admin/components/slides/Charts.vue'),
	},
	{
		name: 'quizes',
		path: '/admin/app/quizes',
		component: require('js/admin/components/quizes/Quizes.vue'),
		children: [
			{
				name: 'quiz-editor',
				path: 'edit/:quizId',
				component: require('js/admin/components/quizes/QuizEditor.vue')
			}
		],
	},
	{
		name: 'dashboard',
		path: '/admin/app'
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
