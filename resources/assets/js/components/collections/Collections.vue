<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="course-sidenav">
				<wnl-sidenav :items="getNavigation()"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" v-bind:class="{'full-width': isMobileProfile}" v-if="!isLoading">
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
				<div>
					<input type="checkbox" name="slides" v-model="selectedPanes" value="slides" v-if="isLargeDesktop">
					<input type="radio" name="slides" v-model="selectedPane" value="slides" v-else>
					<label for="slides">Slajdy / Pytania i Odpowiedzi</label>
					<input type="checkbox" name="quiz" v-model="selectedPanes" value="quiz" v-if="isLargeDesktop">
					<input type="radio" name="slides" v-model="selectedPane" value="quiz" v-else>
					<label for="quiz">Pytania Kontrolne</label>
				</div>
			</div>
			<div class="scrollable-main-container">
				<!-- <wnl-slides-carousel></wnl-slides-carousel> -->
				<wnl-qna-collection v-show="isSlidesPaneVisible"></wnl-qna-collection>
				<wnl-quiz-collection v-show="!isSlidesPaneVisible && isQuizPaneVisible"></wnl-quiz-collection>
			</div>
		</div>
		<wnl-sidenav-slot
			:isVisible="isSlidesPaneVisible && isQuizPaneVisible"
			:isDetached="false"
		>
			<wnl-quiz-collection></wnl-quiz-collection>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

	.collections-header
		padding: $margin-base

	.collections-breadcrumbs
		color: $color-gray-dimmed
		display: flex
		align-items: center


</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import QnaCollection from 'js/components/collections/QnaCollection'
	import QuizCollection from 'js/components/collections/QuizCollection'
	import SlidesCarousel from 'js/components/collections/SlidesCarousel'
	import { resource } from 'js/utils/config'
	import navigation from 'js/services/navigation'
	import { layouts } from 'js/store/modules/ui'

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
				routeName: 'collections-categories',
				selectedPanes: [this.slides, this.quiz],
				selectedPane: this.slides,
				slides: 'slides',
				quiz: 'quiz',
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile', 'isLargeDesktop', 'isTouchScreen', 'currentLayout']),
			...mapGetters('collections', ['isLoading', 'quizQuestionsIds', 'categories', 'qnaQuestionsIds', 'slidesIds']),
			isSlidesPaneVisible() {
				return this.isLargeDesktop ? this.selectedPanes.includes(this.slides) : this.selectedPane === this.slides
			},
			isQuizPaneVisible() {
				return this.isLargeDesktop ? this.selectedPanes.includes(this.quiz) : this.selectedPane === this.quiz
			}
		},
		methods: {
			...mapActions('collections', ['fetchReactions', 'fetchCategories', 'fetchSlidesByTagName']),
			...mapActions('quiz', ['fetchQuestionsCollectionByTagName']),
			...mapActions('qna', ['fetchQuestionsByTagName']),
			getNavigation() {
				let navigation = [];

				this.categories.forEach((rootCategory) => {
					const groupItem = this.getGroupItem({name: rootCategory.name});
					const childItems = rootCategory.categories.map(({name, id}) => this.getChildCategory({name, id, parent: rootCategory.name}));

					navigation = [...navigation, groupItem, ...childItems]
				});

				return navigation
			},
			getGroupItem(group) {
				return navigation.composeItem({
					text: group.name,
					itemClass: 'heading small'
				})
			},
			getChildCategory(childCategory) {
				return navigation.composeItem({
					text: childCategory.name,
					itemClass: 'has-icon',
					routeName: this.routeName,
					routeParams: {
						categoryName: childCategory.name,
						rootCategoryName: childCategory.parent
					},
					iconClass: 'fa-graduation-cap',
					iconTitle: 'Obecna lekcja'
				})
			},
			setupContentForCategory() {
				return this.categoryName && Promise.all([
					this.fetchQuestionsCollectionByTagName({tagName: this.categoryName, ids:this.quizQuestionsIds}),
					this.fetchQuestionsByTagName({tagName: this.categoryName, ids: this.qnaQuestionsIds}),
					// this.fetchSlidesByTagName({tagName: this.categoryName, ids: this.slidesIds})
				])
			},
			navigateToDefaultCategoryIfNone() {
				if (!this.isTouchScreen && !this.categoryName) {
					const firstCategory = this.categories[0].categories[0];

					this.$router.replace({name: this.routeName, params: {
						categoryName: firstCategory.name,
						rootCategoryName: this.categories[0].name
					}})
				}
			},
			adjustPanes() {
				if (!this.isLargeDesktop) {
					this.selectedPane = this.selectedPanes.length === 1 ? this.selectedPanes[0] : this.slides
				} else {
					this.selectedPanes = [this.slides, this.quiz]
				}
			}
		},
		mounted() {
			this.adjustPanes()
			this.fetchCategories()
				.then(this.fetchReactions)
				.then(this.navigateToDefaultCategoryIfNone)
				.then(this.setupContentForCategory)
		},
		watch: {
			'$route' () {
				this.categoryName && this.setupContentForCategory()
			},
			'currentLayout' () {
				this.adjustPanes()
			},
		},
	}
</script>
