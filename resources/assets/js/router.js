import Vue from 'vue'
import Router from 'vue-router'
import {scrollToTop} from 'js/utils/animations'
import {resource} from 'js/utils/config'
import {isProduction} from 'js/utils/env'
import moderatorFeatures from 'js/perimeters/moderator';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import { createSandbox } from 'vue-kindergarten';
import store from 'js/store/store'
import { getCurrentUser } from 'js/services/user';

Vue.use(Router)

let routes = [
	{
		name: 'course',
		path: '/app/courses/:courseId',
		component: require('js/components/course/Course.vue'),
		props: true,
		children: [
			{
				name: resource('courses'),
				path: '',
				component: require('js/components/course/dashboard/Overview.vue'),
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
			{
				name: 'stats',
				path: 'stats',
				component: require('js/components/user/UserStats'),
			},
		]
	},
	{
		name: 'collections',
		path: '/app/collections',
		component: require('js/components/collections/Collections.vue'),
		props: true,
		children: [
			{
				props: true,
				name: 'collections-categories',
				path: ':rootCategoryName/:categoryName',
				component: require('js/components/collections/Collections.vue')
			},
		],
		beforeEnter: (to, from, next) => {
			getCurrentUser().then(({data: currentUser}) => {
				const sandbox = createSandbox(currentUser, {
					perimeters: [currentEditionParticipant],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			})
		},
	},
	{
		name: 'help',
		path: '/app/help',
		component: require('js/components/help/Help.vue'),
		props: true,
		children: [
			{
				name: 'help-learning',
				path: 'learning',
				component: require('js/components/help/LearningHelp.vue'),
			},
			{
				name: 'help-tech',
				path: 'tech',
				component: require('js/components/help/TechnicalHelp.vue'),
			},
			{
				name: 'help-new',
				path: 'new',
				component: require('js/components/help/ComingSoonHelp.vue'),
			},
		]
	},
	{
		path: '/app/questions',
		component: require('js/components/questions/Questions.vue'),
		children: [
			{
				name: 'questions-dashboard',
				path: '',
				component: require('js/components/questions/QuestionsDashboard.vue'),
				props: true,
				children: [
					{
						name: 'quizQuestion',
						path: 'single/:id',
						component: require('js/components/quiz/SingleQuestion.vue'),
					},
				],
			},
			{
				name: 'questions-list',
				path: 'list',
				component: require('js/components/questions/QuestionsList.vue'),
				props: true,
			},
			{
				name: 'questions-planner',
				path: 'plan',
				component: require('js/components/questions/QuestionsPlanner.vue'),
			},
		],
		beforeEnter: (to, from, next) => {
			getCurrentUser().then(({data: currentUser}) => {
				const sandbox = createSandbox(currentUser, {
					perimeters: [currentEditionParticipant],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			})
		},
	},
	{
		name: 'moderatorFeed',
		path: '/app/moderators/feed',
		component: require('js/components/moderators/ModeratorsDashboard.vue'),
		beforeEnter: (to, from, next) => {
			getCurrentUser().then(({data: currentUser}) => {
				const sandbox = createSandbox(currentUser, {
					perimeters: [moderatorFeatures],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			})
		}
	},
	{
		name: 'dashboard',
		path: '/app',
		redirect: {name: 'courses', params: {courseId: 1}},
	},
	{
		name: 'logout',
		path: '/logout',
		beforeEnter: () => {
			document.getElementById('logout-form').submit()
		}
	},
	// {
	// 	name: 'user',
	// 	path: '/app/users/:userId',
	// 	component: require('js/components/users/UserProfile.vue'),
	// },
	{
		path: '/app/users',
		name: 'all-users',
		component: require('js/components/users/MainUsers.vue'),
		props: true,
		redirect: {name: 'user'},
		children: [
			// {
			// 	name: 'all',
			// 	path: 'all',
			// 	component: require('js/components/users/AllUsers.vue')
			// },
			{
				name: 'user',
				path: ':userId',
				component: require('js/components/users/UserProfile.vue'),
			},
		]
	},
	{
		path: '*',
		redirect: '/app/'
	}
]


export default new Router({
	mode: 'history',
	linkActiveClass: 'is-active',
	scrollBehavior: (to, from, savedPosition) => {
		if (to.query && to.query.noScroll) {
			return;
		}

		if (!from.params.hasOwnProperty('slide') || !to.params.hasOwnProperty('slide') ||
			parseInt(from.params.screenId) !== parseInt(to.params.screenId))
		{
			scrollToTop()
			return {x: 0, y: 0}
		}
	},
	routes
})
