import Vue from 'vue';
import Router from 'vue-router';

import Lessons from 'js/admin/components/lessons/Lessons';
import ScreensEditor from 'js/admin/components/lessons/edit/ScreensEditor';
import CourseEditor from 'js/admin/components/courses/CourseEditor';
import StructureEditor from 'js/admin/components/structures/StructureEditor';
import Groups from 'js/admin/components/groups/Groups';
import GroupEditor from 'js/admin/components/groups/GroupEditor';
import EditSlide from 'js/admin/components/slides/EditSlide';
import AddSlide from 'js/admin/components/slides/AddSlide';
import Charts from 'js/admin/components/slides/Charts';
import Annotations from 'js/admin/components/slides/annotations/Annotations';
import QuizQuestionsList from 'js/admin/components/quizes/list/QuizQuestionsList';
import QuizQuestionEdit from 'js/admin/components/quizes/edit/QuizQuestionEdit';
import QuizQuestionCreate from 'js/admin/components/quizes/edit/QuizQuestionCreate';
import FlashcardsSetsList from 'js/admin/components/flashcards/list/FlashcardsSetsList';
import FlashcardsSetEditor from 'js/admin/components/flashcards/edit/FlashcardsSetEditor';
import FlashcardsList from 'js/admin/components/flashcards/list/FlashcardsList';
import FlashcardEditor from 'js/admin/components/flashcards/edit/FlashcardEditor';
import Users from 'js/admin/components/users/Users';
import UserAdd from 'js/admin/components/users/UserAdd';
import UserDetails from 'js/admin/components/users/UserDetails';
import DashboardNews from 'js/admin/components/dashboardNews/DashboardNews';
import DashboardNewsEdit from 'js/admin/components/dashboardNews/DashboardNewsEdit';
import OrdersList from 'js/admin/components/orders/OrdersList';
import TagsList from 'js/admin/components/tags/TagsList';
import TagEditor from 'js/admin/components/tags/TagEditor';
import ContentClassifier from 'js/admin/components/contentClassifier/ContentClassifier';
import TaxonomiesList from 'js/admin/components/taxonomies/TaxonomiesList';
import TaxonomyEditor from 'js/admin/components/taxonomies/TaxonomyEditor';

Vue.use(Router);

let routes = [
	{
		name: 'lessons',
		path: '/admin/app/lessons/:lessonId?',
		component: Lessons,
		props: true,
		children: [
			{
				name: 'screen-edit',
				path: 'screens/:screenId?',
				component: ScreensEditor
			},
		]
	},
	{
		name: 'structure-edit',
		path: '/admin/app/course-structure/:courseId',
		component: StructureEditor,
		props: true,
	},
	{
		name: 'course-edit',
		path: '/admin/app/courses/:id',
		component: CourseEditor,
		props: true,
	},
	{
		name: 'groups',
		path: '/admin/app/groups',
		component: Groups,
		props: true,
	},
	{
		name: 'group-edit',
		path: '/admin/app/groups/:id',
		component: GroupEditor,
		props: true,
	},
	{
		name: 'slides',
		path: '/admin/app/slides/edit/:lessonId?/:screenId?',
		component: EditSlide,
	},
	{
		name: 'add-slide',
		path: '/admin/app/slides/add/:lessonId?/:screenId?',
		component: AddSlide,
	},
	{
		name: 'charts',
		path: '/admin/app/charts',
		component: Charts,
	},
	{
		name: 'annotations',
		path: '/admin/app/annotations',
		component: Annotations,
	},
	{
		name: 'quizes',
		path: '/admin/app/quizes',
		component: QuizQuestionsList,
		children: [
			{
				name: 'quiz-editor',
				path: 'edit/:quizId',
				component: QuizQuestionEdit,
			},
			{
				name: 'quiz-creator',
				path: 'new',
				component: QuizQuestionCreate
			}
		],
	},
	{
		name: 'flashcards-sets',
		path: '/admin/app/flashcards-sets',
		component: FlashcardsSetsList,
	},
	{
		name: 'flashcards-sets-edit',
		path: '/admin/app/flashcards-sets/:flashcardsSetId',
		component: FlashcardsSetEditor,
		props: true,
	},
	{
		name: 'flashcards',
		path: '/admin/app/flashcards',
		component: FlashcardsList,
	},
	{
		name: 'flashcards-edit',
		path: '/admin/app/flashcards/:flashcardId',
		component: FlashcardEditor,
		props: true,
	},
	{
		name: 'users',
		path: '/admin/app/users',
		component: Users,
	},
	{
		name: 'users-add',
		path: '/admin/app/users/add',
		component: UserAdd,
	},
	{
		name: 'user-details',
		path: '/admin/app/users/:userId',
		component: UserDetails,
	},
	{
		name: 'dashboard-news',
		path: '/admin/app/dashboard-news',
		component: DashboardNews,
	},
	{
		name: 'dashboard-news-edit',
		path: '/admin/app/dashboard-news/:id',
		component: DashboardNewsEdit,
		props: true,
	},
	{
		name: 'orders',
		path: '/admin/app/orders',
		component: OrdersList,
	},
	{
		name: 'tags',
		path: '/admin/app/tags',
		component: TagsList,
	},
	{
		name: 'tag-edit',
		path: '/admin/app/tags/:id',
		component: TagEditor,
		props: true,
	},
	{
		name: 'content-classifier',
		path: '/admin/app/content-classifier',
		component: ContentClassifier,
	},
	{
		name: 'taxonomies',
		path: '/admin/app/taxonomies',
		component: TaxonomiesList,
	},
	{
		name: 'taxonomy-edit',
		path: '/admin/app/taxonomies/:id',
		component: TaxonomyEditor,
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
