import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

// TODO fix require in the entire repo
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
		name: 'course-edit',
		path: '/admin/app/courses/:id',
		component: require('js/admin/components/courses/CourseEditor'),
		props: true,
	},
	{
		name: 'groups',
		path: '/admin/app/groups',
		component: require('js/admin/components/groups/Groups.vue'),
		props: true,
	},
	{
		name: 'group-edit',
		path: '/admin/app/groups/:id',
		component: require('js/admin/components/groups/GroupEditor'),
		props: true,
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
		name: 'users',
		path: '/admin/app/users',
		component: require('js/admin/components/users/Users.vue'),
	},
	{
		name: 'users-add',
		path: '/admin/app/users/add',
		component: require('js/admin/components/users/UserAdd.vue'),
	},
	{
		name: 'user-details',
		path: '/admin/app/users/:userId',
		component: require('js/admin/components/users/UserDetails.vue'),
	},
	{
		name: 'dashboard-news',
		path: '/admin/app/dashboard-news',
		component: require('js/admin/components/dashboardNews/DashboardNews.vue'),
	},
	{
		name: 'dashboard-news-edit',
		path: '/admin/app/dashboard-news/:id',
		component: require('js/admin/components/dashboardNews/DashboardNewsEdit.vue'),
		props: true,
	},
	{
		name: 'orders',
		path: '/admin/app/orders',
		component: require('js/admin/components/orders/OrdersList'),
	},
	{
		name: 'tags',
		path: '/admin/app/tags',
		component: require('js/admin/components/tags/TagsList'),
	},
	{
		name: 'tag-edit',
		path: '/admin/app/tags/:id',
		component: require('js/admin/components/tags/TagEditor'),
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
			document.getElementById('logout-form').submit();
		}
	},
];

export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: () => {
		return {x: 0, y: 0};
	},
	routes
});
