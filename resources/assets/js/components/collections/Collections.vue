<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="collections-sidenav">
				<wnl-sidenav :items="getNavigation()"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" v-bind:class="{'full-width': isTouchScreen}" v-if="!isLoading">
			<div class="scrollable-main-container">
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
						<a v-for="name, panel in panels" class="panel-toggle" :class="{'is-active': isPanelActive(panel), 'is-single': isSinglePanelView}"  :key="panel" @click="togglePanel(panel)">
							{{name}}
							<!-- <span class="icon is-small">
								<i class="fa" :class="[isPanelActive(panel) ? 'fa-check-circle' : 'fa-circle-o']"></i>
							</span> -->
						</a>
					</div>
				</div>
				<div class="columns">
					<div class="column" v-show="isSlidesPanelVisible">
						<!-- <wnl-slides-carousel></wnl-slides-carousel> -->
						<wnl-qna-collection></wnl-qna-collection>
					</div>
					<div class="column" v-show="isQuizPanelVisible">
						<wnl-quiz-collection></wnl-quiz-collection>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

	.collections-header
		border-bottom: $border-light-gray
		display: block

	.collections-sidenav
		flex: 1
		min-width: $sidenav-min-width
		overflow: auto
		padding: 7px 0
		width: $sidenav-width

	.collections-breadcrumbs
		align-items: center
		color: $color-gray-dimmed
		display: flex
		margin-right: $margin-base

	.collections-controls
		align-items: center
		display: flex
		flex-wrap: wrap
		margin-bottom: $margin-base
		user-select: none

		.panel-toggle
			border: $border-light-gray
			border-radius: $border-radius-small
			color: $color-gray-dimmed
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			margin: $margin-base $margin-small 0 0
			padding: $margin-small
			text-transform: uppercase
			transition: background $transition-length-base

			&:hover
				background: $color-light-gray
				transition: background $transition-length-base

			&:last-child
				margin-right: 0

			&.is-active
				background: $color-ocean-blue
				border-color: $color-ocean-blue
				color: $color-white
				opacity: 1
				transition: opacity $transition-length-base

				&:hover
					opacity: 0.5
					transition: opacity $transition-length-base

			&.is-single
				font-size: $font-size-minus-3

			.icon
				margin-left: $margin-tiny

	.columns
		justify-content: space-around

	.column
		max-width: $course-content-max-width
</style>

<script>
	import { pull } from 'lodash'
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
				activePanels: ['slides', 'quiz'],
				routeName: 'collections-categories',
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobile', 'isLargeDesktop', 'isTouchScreen', 'currentLayout']),
			...mapGetters('collections', ['isLoading', 'quizQuestionsIds', 'categories', 'qnaQuestionsIds', 'slidesIds']),
			isQuizPanelVisible() {
				return this.isPanelActive('quiz')
			},
			isSlidesPanelVisible() {
				return this.isPanelActive('slides')
			},
			isSinglePanelView() {
				return this.isMobile
			},
			panels() {
				return {
					slides: 'Pytania i odpowiedzi',
					quiz: 'Pytania kontrolne',
				}
			},
		},
		methods: {
			...mapActions('collections', ['fetchReactions', 'fetchCategories', 'fetchSlidesByTagName']),
			...mapActions('quiz', ['fetchQuestionsCollectionByTagName']),
			...mapActions('qna', ['fetchQuestionsByTagName']),
			getNavigation() {
				let navigation = []

				this.categories.forEach((rootCategory) => {
					const groupItem = this.getGroupItem({name: rootCategory.name});
					const childItems = rootCategory.categories.map(({name, id}) => this.getChildCategory({name, id, parent: rootCategory.name}));

					navigation = [...navigation, groupItem, ...childItems]
				})

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
					iconClass: 'fa-angle-right',
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
			togglePanel(panel) {
				if (this.isSinglePanelView) {
					this.activePanels = [panel]
					return
				}

				let index = this.activePanels.indexOf(panel)
				if (index > -1 && this.activePanels.length > 1) {
					this.activePanels.splice(index, 1)
				} else if (index === -1) {
					this.activePanels.push(panel)
				} else {
					let other = pull(Object.keys(this.panels), panel)
					if (other.length > 0) {
						this.activePanels = [other[0]]
					}
				}
			},
			isPanelActive(panel) {
				if (this.isSinglePanelView) {
					return this.activePanels[0] === panel
				}

				return this.activePanels.includes(panel)
			},
		},
		mounted() {
			this.fetchCategories()
				.then(this.fetchReactions)
				.then(this.navigateToDefaultCategoryIfNone)
				.then(this.setupContentForCategory)
		},
		watch: {
			'$route' () {
				this.categoryName && this.setupContentForCategory()
			},
		},
	}
</script>
