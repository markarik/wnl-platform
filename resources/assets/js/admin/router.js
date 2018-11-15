import Vue from 'vue'
import Router from 'vue-router'

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
		component: require('js/admin/components/slides/EditSlide.vue'),
	},
	{
		name: 'add-slide',
		path: '/admin/app/slides/add/:lessonId?/:screenId?',
		component: require('js/admin/components/slides/AddSlide.vue'),
	},
	{
		name: 'charts',
		path: '/admin/app/charts',
		component: require('js/admin/components/slides/Charts.vue'),
	},
	{
		name: 'annotations',
		path: '/admin/app/annotations',
		component: require('js/admin/components/slides/annotations/Annotations.vue'),
	},
	{
		name: 'quizes',
		path: '/admin/app/quizes',
		component: require('js/admin/components/quizes/QuizQuestions.vue'),
		children: [
			{
				name: 'quiz-editor',
				path: 'edit/:quizId',
				component: require('js/admin/components/quizes/QuizQuestionEdit.vue')
			},
			{
				name: 'quiz-creator',
				path: 'new',
				component: require('js/admin/components/quizes/QuizQuestionCreate.vue')
			}
		],
	},
	{
		name: 'flashcards-sets',
		path: '/admin/app/flashcards-sets',
		component: require('js/admin/components/flashcards/list/FlashcardsSetsList.vue'),
	},
	{
		name: 'flashcards-sets-edit',
		path: '/admin/app/flashcards-sets/:flashcardsSetId',
		component: require('js/admin/components/flashcards/edit/FlashcardsSetEditor'),
		props: true,
	},
	{
		name: 'flashcards',
		path: '/admin/app/flashcards',
		component: require('js/admin/components/flashcards/list/FlashcardsList'),
	},
	{
		name: 'flashcards-edit',
		path: '/admin/app/flashcards/:flashcardId',
		component: require('js/admin/components/flashcards/edit/FlashcardEditor'),
		props: true,
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
