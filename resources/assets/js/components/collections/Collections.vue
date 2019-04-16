<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside collections-sidenav">
				<wnl-sidenav :items="getNavigation()" :options="navigationOptions"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isTouchScreen}" v-if="!isLoading">
			<div class="scrollable-main-container" v-if="rootCategoryName && categoryName">
				<div class="collections-header">
					<div class="collections-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-star-o"></i></span>
						</div>
						<div class="breadcrumb" v-if="rootCategoryName">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{rootCategoryName}}</span>
						</div>
						<div class="breadcrumb" v-if="categoryName">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{categoryName}}</span>
						</div>
					</div>
					<div class="collections-controls">
						<a v-for="(name, panel) in panels" class="panel-toggle" :class="{'is-active': isPanelActive(panel), 'is-single': isSinglePanelView}" :key="panel" @click="togglePanel(panel)">
							{{name}}
							<span class="icon is-small">
								<i class="fa" :class="[isPanelActive(panel) ? 'fa-check-circle' : 'fa-circle-o']"></i>
							</span>
						</a>
					</div>
				</div>
				<div class="columns">
					<div class="column" v-show="isSlidesPanelVisible">
						<wnl-slides-carousel
							:category-id="categoryId"
							:category-name="categoryName"
							:root-category-name="rootCategoryName"
							:saved-slides-count="slidesIds.length"
							:slides-ids="slidesIds"
							@userEvent="onUserEvent"
						></wnl-slides-carousel>
						<wnl-qna-collection
							:category-name="categoryName"
							:root-category-name="rootCategoryName"
						></wnl-qna-collection>
					</div>
					<div class="column" v-show="isQuizPanelVisible">
						<wnl-quiz-collection
							:category-name="categoryName"
							:root-category-name="rootCategoryName"
							:quiz-questions-ids="quizQuestionsIds"
							@changeQuizQuestionsPage="onChangeQuizQuestionsPage"
							@userEvent="onUserEvent"
						></wnl-quiz-collection>
					</div>
				</div>
			</div>
			<div v-else class="collections-placeholder">
				<span class="icon main"><i class="fa fa-star-o"></i></span>
				<span class="welcome">Witaj w Kolekcjach!</span>
				<span>Wybierz temat z menu <span class="icon is-small" v-if="isTouchScreen"><i class="fa fa-bars"></i></span> i&nbsp;przeglądaj&nbsp;zapisane&nbsp;fragmenty&nbsp;kursu</span>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

		&.full-width

			.column
				width: 100%

	.full-width
		width: 100vw

	.collections-placeholder
		align-items: center
		color: $color-gray
		display: flex
		flex-direction: column
		font-size: $font-size-plus-1
		height: 100%
		min-height: 50vh
		justify-content: center
		padding: $margin-big
		text-align: center
		width: 100%

		.icon.main
			height: $font-size-plus-7
			width: $font-size-plus-7

			.fa
				font-size: $font-size-plus-7

		.welcome
			font-size: $font-size-plus-3
			font-weight: $font-weight-light
			line-height: $line-height-plus

	.collections-header
		border-bottom: $border-light-gray
		display: block

	.collections-sidenav
		flex: 1
		min-width: $sidenav-min-width
		overflow: auto
		padding: $margin-small 0
		width: $sidenav-width

	.collections-breadcrumbs
		align-items: center
		color: $color-gray
		display: flex
		margin-right: $margin-base

		.breadcrumb
			max-width: 200px
			overflow-x: hidden
			text-overflow: ellipsis
			white-space: nowrap

	.collections-controls
		align-items: center
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base

	.columns
		justify-content: space-around

	.column
		max-width: $course-content-max-width
		width: 50%
</style>

<script>
import { pull } from 'lodash';
import { mapActions, mapGetters } from 'vuex';

import features from 'js/consts/events_map/features.json';
import context from 'js/consts/events_map/context.json';
import Sidenav from 'js/components/global/Sidenav';
import SidenavSlot from 'js/components/global/SidenavSlot';
import MainNav from 'js/components/MainNav';
import QnaCollection from 'js/components/collections/QnaCollection';
import QuizCollection from 'js/components/collections/QuizCollection';
import SlidesCarousel from 'js/components/collections/SlidesCarousel';
import navigation from 'js/services/navigation';

export default {
	props: ['categoryName', 'rootCategoryName'],
	components: {
		'wnl-sidenav': Sidenav,
		'wnl-sidenav-slot': SidenavSlot,
		'wnl-main-nav': MainNav,
		'wnl-qna-collection': QnaCollection,
		'wnl-quiz-collection': QuizCollection,
		'wnl-slides-carousel': SlidesCarousel
	},
	data() {
		return {
			activePanels: ['slides', 'quiz'],
			routeName: 'collections-categories',
		};
	},
	computed: {
		...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isLargeDesktop', 'isTouchScreen', 'currentLayout']),
		...mapGetters('collections', [
			'isLoading',
			'getQuizQuestionsIdsForCategory',
			'categories',
			'getQnaQuestionsIdsForCategory',
			'getSlidesIdsForCategory',
			'getCategoryByName',
			'getItemsCount'
		]),
		navigationOptions() {
			return {
				hasGroups: true,
				forceGroupsOpen: true,
			};
		},
		isQuizPanelVisible() {
			return this.isPanelActive('quiz');
		},
		isSlidesPanelVisible() {
			return this.isPanelActive('slides');
		},
		isSinglePanelView() {
			return this.isTouchScreen;
		},
		panels() {
			return {
				slides: 'Slajdy + pytania',
				quiz: 'Pytania kontrolne',
			};
		},
		categoryId() {
			const rootCategoryObject = this.categories
				.find((category) => category.name === this.rootCategoryName);

			const categoryObject = rootCategoryObject
					&& rootCategoryObject.categories.find((category) => category.name === this.categoryName);

			return categoryObject && categoryObject.id;
		},
		quizQuestionsIds() {
			return this.getQuizQuestionsIdsForCategory(this.categoryName);
		},
		qnaQuestionsIds() {
			return this.getQnaQuestionsIdsForCategory(this.categoryName);
		},
		slidesIds() {
			return this.getSlidesIdsForCategory(this.categoryName);
		}
	},
	methods: {
		...mapActions('collections', ['fetchReactions', 'fetchCategories', 'fetchSlidesByTagName']),
		...mapActions('quiz', { 'fetchQuiz': 'fetchQuestionsCollectionByTagName', 'resetQuiz': 'resetState' }),
		...mapActions('qna', { 'fetchQna':'fetchQuestionsByTagName', 'resetQna': 'destroyQna' }),
		...mapActions(['toggleOverlay']),
		getNavigation() {
			let navigation = [];

			this.categories.forEach((rootCategory) => {
				const groupItem = this.getGroupItem({ name: rootCategory.name });
				const childItems = rootCategory.categories
					.map(({ name, id }) => this.getChildCategory({ name, id, parent: rootCategory.name }));

				groupItem.subitems = childItems;

				navigation = [...navigation, groupItem];
			});

			return navigation;
		},
		getGroupItem(group) {
			return navigation.composeItem({
				text: group.name,
				itemClass: 'heading small'
			});
		},
		getChildCategory(childCategory) {
			return navigation.composeItem({
				text: childCategory.name,
				meta: `(${this.getItemsCount(childCategory.name)})`,
				itemClass: 'has-icon',
				routeName: this.routeName,
				routeParams: {
					categoryName: childCategory.name,
					rootCategoryName: childCategory.parent
				},
				iconClass: 'fa-angle-right',
			});
		},
		setupContentForCategory() {
			const contentToFetch = [];

			if (this.quizQuestionsIds.length) {
				contentToFetch.push(this.fetchQuiz({ tagName: this.categoryName, ids: this.quizQuestionsIds }));
			} else {
				this.resetQuiz();
			}

			if (this.qnaQuestionsIds.length) {
				contentToFetch.push(this.fetchQna({ tagName: this.categoryName, ids: this.qnaQuestionsIds }));
			} else {
				this.resetQna();
			}

			if (this.slidesIds.length) {
				contentToFetch.push(this.fetchSlidesByTagName({ tagName: this.categoryName, ids: this.slidesIds }));
			}

			this.categoryId && this.$trackUserEvent({
				context: context.collections.value,
				feature: features.category.value,
				action: features.category.actions.open.value,
				target: this.categoryId
			});

			return this.categoryName && Promise.all(contentToFetch);
		},
		onUserEvent(payload) {
			this.$trackUserEvent({
				context: context.collections.value,
				...payload,
			});
		},
		togglePanel(panel) {
			if (this.isSinglePanelView) {
				return this.activePanels = [panel];
			}

			let index = this.activePanels.indexOf(panel);
			if (index > -1 && this.activePanels.length > 1) {
				this.activePanels.splice(index, 1);
			} else if (index === -1) {
				this.activePanels.push(panel);
			} else {
				let other = pull(Object.keys(this.panels), panel);
				if (other.length > 0) {
					this.activePanels = [other[0]];
				}
			}
		},
		isPanelActive(panel) {
			if (this.isSinglePanelView) {
				return this.activePanels[0] === panel;
			}

			return this.activePanels.includes(panel);
		},
		onChangeQuizQuestionsPage(page) {
			this.fetchQuiz({ tagName: this.categoryName, ids: this.quizQuestionsIds, page });
		}
	},
	mounted() {
		this.toggleOverlay({ source: 'collections', display: true });
		this.fetchCategories()
			.then(this.fetchReactions)
			.then(this.setupContentForCategory)
			.then(() => this.toggleOverlay({ source: 'collections', display: false }));
	},
	watch: {
		categoryName(newCategoryName, oldCategoryName) {
			if (oldCategoryName !== newCategoryName) {
				this.setupContentForCategory();
			}
		},
		rootCategoryName(newRootCategoryName, oldRootCategoryName) {
			if (oldRootCategoryName !== newRootCategoryName) {
				this.setupContentForCategory();
			}
		},
	},
};
</script>
