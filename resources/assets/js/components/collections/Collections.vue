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
	import { resource } from 'js/utils/config'
	import navigation from 'js/services/navigation'

	export default {
		props: ['courseId', 'categoryName'],
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav,
			'wnl-qna-collection': QnaCollection,
			'wnl-quiz-collection': QuizCollection
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			...mapGetters('collections', ['isLoading', 'quizQuestionsIds', 'categories', 'qnaQuestionsIds']),
		},
		methods: {
			...mapActions('collections', ['fetchReactions', 'fetchCategories']),
			...mapActions('quiz', ['fetchQuestionsCollection']),
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
					routeName: 'collections-categories',
					routeParams: {
						categoryName: childCategory.name,
					},
					iconClass: 'fa-graduation-cap',
					iconTitle: 'Obecna lekcja'
				})
			},
		},

		mounted() {
			this.fetchCategories()
				.then(this.fetchReactions)
				.then(() => this.fetchQuestionsCollection(this.quizQuestionsIds))
				.then(() => this.fetchQuestionsByTagName({tagName: this.categoryName, ids: this.qnaQuestionsIds}))
		}
	}
</script>
