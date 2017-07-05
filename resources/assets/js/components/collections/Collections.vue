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
			<div class="scrollable-main-container">
				<wnl-slides-carousel></wnl-slides-carousel>
				<wnl-qna-collection></wnl-qna-collection>
			</div>
		</div>
		<wnl-sidenav-slot
			:isVisible="true"
			:isDetached="false"
		>
			<wnl-quiz-collection></wnl-quiz-collection>
		</wnl-sidenav-slot>

	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		padding: $margin-small

	.wnl-middle
		border-right: $border-light-gray
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

	export default {
		props: ['courseId', 'categoryName'],
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
				routeName: 'collections-categories'
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			...mapGetters('collections', ['isLoading', 'quizQuestionsIds', 'categories', 'qnaQuestionsIds']),
		},
		methods: {
			...mapActions('collections', ['fetchReactions', 'fetchCategories']),
			...mapActions('quiz', ['fetchQuestionsCollectionByTagName']),
			...mapActions('qna', ['fetchQuestionsByTagName']),
			getNavigation() {
				let navigation = [];

				this.categories.forEach(({name, id, categories: childCategories}) => {
					const groupItem = this.getGroupItem({name});
					const childItems = childCategories.map(({name, id}) => this.getChildCategory({name, id}));

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
					},
					iconClass: 'fa-graduation-cap',
					iconTitle: 'Obecna lekcja'
				})
			},
			setupContentForCategory() {
				return Promise.all([
					this.fetchQuestionsCollectionByTagName({tagName: this.categoryName, ids:this.quizQuestionsIds}),
					this.fetchQuestionsByTagName({tagName: this.categoryName, ids: this.qnaQuestionsIds})
				])
			},
			navigateToDefaultCategoryIfNone() {
				if (!this.categoryName) {
					const firstCategory = this.categories[0].categories[0];

					this.$router.replace({name: this.routeName, params: {
						categoryName: firstCategory.name
					}})
				}
			}
		},

		mounted() {
			this.fetchCategories()
				.then(this.fetchReactions)
				.then(this.navigateToDefaultCategoryIfNone)
				.then(this.setupContentForCategory)
		},
		watch: {
			'$route' () {
				this.setupContentForCategory()
			}
		},
	}
</script>
