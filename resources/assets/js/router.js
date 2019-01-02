import Vue from 'vue';
import Router from 'vue-router';
import store from 'js/store/store';
import {scrollToTop} from 'js/utils/animations';
import {resource} from 'js/utils/config';
import moderatorFeatures from 'js/perimeters/moderator';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import {createSandbox} from 'vue-kindergarten';
import {getApiUrl} from 'js/utils/env';

Vue.use(Router);

let routes = [
	{
		name: 'course',
		path: '/app/courses/:courseId',
		component: require('js/components/course/Course.vue').default,
		props: true,
		children: [
			{
				name: resource('courses'),
				path: '',
				component: require('js/components/course/dashboard/Overview.vue').default,
				props: true,
			},
			{
				name: resource('lessons'),
				path: '/app/courses/:courseId/lessons/:lessonId',
				component: require('js/components/course/Lesson.vue').default,
				props: true,
				children: [
					{
						name: resource('screens'),
						path: 'screens/:screenId/:slide?',
						component: require('js/components/course/Screen.vue').default,
						props: true,
					}
				],
				beforeEnter: (to, from, next) => {
					store.dispatch('setupCurrentUser').then(() => {
						const sandbox = createSandbox(store.getters.currentUser, {
							perimeters: [currentEditionParticipant],
						});

						if (!sandbox.isAllowed('access')) {
							return next('/');
						}
						return next();
					});
				},
			}
		]
	},
	{
		name: 'myself',
		path: '/app/myself',
		component: require('js/components/user/Myself.vue').default,
		props: true,
		children: [
			{
				name: 'my-orders',
				path: 'orders',
				component: require('js/components/user/MyOrders.vue').default
			},
			{
				name: 'my-profile',
				path: 'profile',
				component: require('js/components/user/MyProfile.vue').default
			},
			{
				name: 'personal-data',
				path: 'personal-data',
				component: require('js/components/user/PersonalData.vue').default
			},
			{
				name: 'my-billing-data',
				path: 'billing',
				component: require('js/components/user/MyBillingData.vue').default
			},
			{
				name: 'my-settings',
				path: 'settings',
				component: require('js/components/user/MySettings.vue').default
			},
			{
				name: 'my-password',
				path: 'password',
				component: require('js/components/user/MyPassword.vue').default
			},
			{
				name: 'stats',
				path: 'stats',
				component: require('js/components/user/UserStats.vue').default,
			},
			{
				name: 'certificates',
				path: 'certificates',
				component: require('js/components/user/UserCertificates.vue').default,
			},
			{
				name: 'lessons-availabilites',
				path: 'availabilities',
				component: require('js/components/user/plan/PlanView.vue').default,
			},
			{
				name: 'progress-reset',
				path: 'progress-reset',
				component: require('js/components/user/ProgressReset').default,
			},
			{
				name: 'delete-account',
				path: 'delete-account',
				component: require('js/components/user/DeleteAccount').default,
			}
		]
	},
	{
		name: 'collections',
		path: '/app/collections',
		component: require('js/components/collections/Collections.vue').default,
		props: true,
		children: [
			{
				props: true,
				name: 'collections-categories',
				path: ':rootCategoryName/:categoryName',
				component: require('js/components/collections/Collections.vue').default
			},
		],
		beforeEnter: (to, from, next) => {
			store.dispatch('setupCurrentUser').then(() => {
				const sandbox = createSandbox(store.getters.currentUser, {
					perimeters: [currentEditionParticipant],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			});
		},
	},
	{
		name: 'help',
		path: '/app/help',
		component: require('js/components/help/Help.vue').default,
		redirect: {name: 'help-new'},
		children: [
			{
				name: 'help-learning',
				path: 'learning',
				component: require('js/components/global/Page.vue').default,
			},
			{
				name: 'help-tech',
				path: 'tech',
				component: require('js/components/global/Page.vue').default,
			},
			{
				name: 'help-new',
				path: 'new',
				component: require('js/components/global/Page.vue').default,
			},
			{
				name: 'help-service',
				path: 'service',
				component: require('js/components/global/Page.vue').default,
			},
			{
				name: 'satisfaction-guarantee',
				path: 'guarantee',
				component: require('js/components/global/Page.vue').default,
			}
		]
	},
	{
		path: '/app/questions',
		component: require('js/components/questions/Questions.vue').default,
		children: [
			{
				name: 'questions-dashboard',
				path: '',
				component: require('js/components/questions/QuestionsDashboard.vue').default,
				props: true,
				children: [
					{
						name: 'quizQuestion',
						path: 'single/:id',
						component: require('js/components/quiz/SingleQuestionRedirect.vue').default,
					},
				],
			},
			{
				props: true,
				name: 'quiz-question',
				path: 'single/:quizQuestionId',
				component: require('js/components/quiz/SingleQuestion.vue').default,
			},
			{
				name: 'questions-list',
				path: 'list',
				component: require('js/components/questions/QuestionsList.vue').default,
				props: true,
			},
			{
				name: 'questions-planner',
				path: 'plan',
				component: require('js/components/questions/QuestionsPlanner.vue').default,
			},
			{
				name: 'messages',
				path: '/app/messages',
				component: require('js/components/messages/MessagesDashboard.vue').default,
			},

		],
		beforeEnter: (to, from, next) => {
			store.dispatch('setupCurrentUser').then(() => {
				const sandbox = createSandbox(store.getters.currentUser, {
					perimeters: [currentEditionParticipant],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			});
		},
	},
	{
		name: 'moderatorFeed',
		path: '/app/moderators/feed',
		component: require('js/components/moderators/ModeratorsDashboard.vue').default,
		beforeEnter: (to, from, next) => {
			store.dispatch('setupCurrentUser').then(() => {
				const sandbox = createSandbox(store.getters.currentUser, {
					perimeters: [moderatorFeatures],
				});

				if (!sandbox.isAllowed('access')) {
					return next('/');
				}
				return next();
			});
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
			document.getElementById('logout-form').submit();
		}
	},
	{
		path: '/app/users',
		name: 'all-users',
		component: require('js/components/users/MainUsers.vue').default,
		props: true,
		redirect: {name: 'user'},
		children: [
			{
				name: 'user',
				path: ':userId',
				component: require('js/components/users/UserProfile.vue').default,
			},
		]
	},
	{
		name: 'dynamicContextMiddleRoute',
		path: '/app/dynamic/:resource/:context',
		beforeEnter: (to, from, next) => {
			axios.post(getApiUrl(`${to.params.resource}/.context`), {
				context: to.params.context
			}).then(({data}) => {
				return next({
					...data,
					query: to.query
				});
			}).catch(err => {
				return next(from);
			});
		}

	},
	{
		path: '*',
		redirect: '/app/'
	}
];

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
			scrollToTop();
			return {x: 0, y: 0};
		}
	},
	routes
});
