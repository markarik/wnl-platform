import Vue from 'vue';
import Router from 'vue-router';
import store from 'js/store/store';
import {scrollToTop} from 'js/utils/animations';
import {resource} from 'js/utils/config';
import moderatorFeatures from 'js/perimeters/moderator';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import {createSandbox} from 'vue-kindergarten';
import {getApiUrl} from 'js/utils/env';

import Course from 'js/components/course/Course.vue';
import Overview from 'js/components/course/dashboard/Overview.vue';
import Lesson from 'js/components/course/Lesson.vue';
import Screen from 'js/components/course/Screen.vue';
import Myself from 'js/components/user/Myself.vue';
import MyOrders from 'js/components/user/MyOrders.vue';
import MyProfile from 'js/components/user/MyProfile';
import PersonalData from 'js/components/user/PersonalData';
import MyBillingData from 'js/components/user/MyBillingData';
import MySettings from 'js/components/user/MySettings';
import MyPassword from 'js/components/user/MyPassword';
import UserStats from 'js/components/user/UserStats';
import UserCertificates from 'js/components/user/UserCertificates';
import PlanView from 'js/components/user/plan/PlanView';
import ProgressReset from 'js/components/user/ProgressReset';
import DeleteAccount from 'js/components/user/DeleteAccount';
import Collections from 'js/components/collections/Collections';
import Help from 'js/components/help/Help';
import Page from 'js/components/global/Page';
import Questions from 'js/components/questions/Questions';
import QuestionsDashboard from 'js/components/questions/QuestionsDashboard';
import SingleQuestionRedirect from 'js/components/quiz/SingleQuestionRedirect';
import SingleQuestion from 'js/components/quiz/SingleQuestion';
import QuestionsList from 'js/components/questions/QuestionsList';
import QuestionsPlanner from 'js/components/questions/QuestionsPlanner';
import MessagesDashboard from 'js/components/messages/MessagesDashboard';
import ModeratorsDashboard from 'js/components/moderators/ModeratorsDashboard';
import MainUsers from 'js/components/users/MainUsers';
import UserProfile from 'js/components/users/UserProfile';
import Onboarding from 'js/components/onboarding/Onboarding';
import {ONBOARDING_STEPS, ROLES} from 'js/consts/user';

Vue.use(Router);

let routes = [
	{
		name: 'course',
		path: '/app/courses/:courseId',
		component: Course,
		props: true,
		meta: {
			requiresOnboardingPassed: true,
		},
		children: [
			{
				name: resource('courses'),
				path: '',
				component: Overview,
				props: true,
			},
			{
				name: resource('lessons'),
				path: '/app/courses/:courseId/lessons/:lessonId',
				component: Lesson,
				props: true,
				children: [
					{
						name: resource('screens'),
						path: 'screens/:screenId/:slide?',
						component: Screen,
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
		],
	},
	{
		name: 'myself',
		path: '/app/myself',
		component: Myself,
		props: true,
		children: [
			{
				name: 'my-orders',
				path: 'orders',
				component: MyOrders
			},
			{
				name: 'my-profile',
				path: 'profile',
				component: MyProfile
			},
			{
				name: 'personal-data',
				path: 'personal-data',
				component: PersonalData
			},
			{
				name: 'my-billing-data',
				path: 'billing',
				component: MyBillingData
			},
			{
				name: 'my-settings',
				path: 'settings',
				component: MySettings
			},
			{
				name: 'my-password',
				path: 'password',
				component: MyPassword
			},
			{
				name: 'stats',
				path: 'stats',
				component: UserStats
			},
			{
				name: 'certificates',
				path: 'certificates',
				component: UserCertificates,
				meta: {
					requiresOnboardingPassed: true,
				},
			},
			{
				name: 'lessons-availabilites',
				path: 'availabilities',
				component: PlanView,
				meta: {
					requiresOnboardingPassed: true,
				},
			},
			{
				name: 'progress-reset',
				path: 'progress-reset',
				component: ProgressReset,
				meta: {
					requiresOnboardingPassed: true,
				},
			},
			{
				name: 'delete-account',
				path: 'delete-account',
				component: DeleteAccount
			}
		]
	},
	{
		name: 'collections',
		path: '/app/collections',
		component: Collections,
		props: true,
		meta: {
			requiresOnboardingPassed: true,
		},
		children: [
			{
				props: true,
				name: 'collections-categories',
				path: ':rootCategoryName/:categoryName',
				component: Collections
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
		component: Help,
		redirect: {name: 'help-new'},
		children: [
			{
				name: 'help-learning',
				path: 'learning',
				component: Page,
			},
			{
				name: 'help-tech',
				path: 'tech',
				component: Page,
			},
			{
				name: 'help-new',
				path: 'new',
				component: Page,
			},
			{
				name: 'help-service',
				path: 'service',
				component: Page,
			},
			{
				name: 'satisfaction-guarantee',
				path: 'guarantee',
				component: Page,
			},
			{
				name: 'key-shortcuts',
				path: 'shortcuts',
				component: Page,
			}
		]
	},
	{
		path: '/app/questions',
		component: Questions,
		meta: {
			requiresOnboardingPassed: true,
		},
		children: [
			{
				name: 'questions-dashboard',
				path: '',
				component: QuestionsDashboard,
				props: true,
				children: [
					{
						name: 'quizQuestion',
						path: 'single/:id',
						component: SingleQuestionRedirect,
					},
				],
			},
			{
				props: true,
				name: 'quiz-question',
				path: 'single/:quizQuestionId',
				component: SingleQuestion,
			},
			{
				name: 'questions-list',
				path: 'list',
				component: QuestionsList,
				props: true,
			},
			{
				name: 'questions-planner',
				path: 'plan',
				component: QuestionsPlanner,
			},
			{
				name: 'messages',
				path: '/app/messages',
				component: MessagesDashboard,
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
		component: ModeratorsDashboard,
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
		name: 'onboarding',
		path: '/app/onboarding/:step?',
		component: Onboarding,
		props: true,
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
		component: MainUsers,
		props: true,
		redirect: {name: 'user'},
		children: [
			{
				name: 'user',
				path: ':userId',
				component: UserProfile,
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

const router =  new Router({
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

router.beforeEach(async (to, from, next) => {
	await store.dispatch('setupCurrentUser');
	const currentUser = store.getters.currentUser;

	if (
		to.matched.some(record => record.meta.requiresOnboardingPassed) &&
		currentUser.productState.wizard_step !== ONBOARDING_STEPS.FINISHED && !currentUser.roles.includes(ROLES.ADMIN)
	) {
		return next('/app/onboarding');
	}
	return next();
});

export default router;
