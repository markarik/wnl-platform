<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<!-- TODO build navigation base on tags -->
			<aside class="wnl-sidenav">
				<wnl-sidenav :items="items"></wnl-sidenav>
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
	import QuizCollection from 'js/components/collections/QuizCollection';

	export default {
		props: ['view'],
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav,
			'wnl-qna-collection': QnaCollection,
			'wnl-quiz-collection': QuizCollection
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			...mapGetters('collections', ['isLoading', 'quizQuestionsIds']),
			items() {
				let items = [
					{
						text: 'Kolekcje',
						itemClass: 'heading small',
					},
					{
						text: 'Slajdy',
						itemClass: 'has-icon',
						to: {
							name: 'collection-slides',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-television',
						iconTitle: 'Twoja kolekcja slajdów',
					},
				]

				return items
			}
		},
		methods: {
			...mapActions('collections', ['fetchReactions']),
			...mapActions('quiz', ['fetchQuestionsCollection']),
		},
		mounted() {
			// TODO fetch reactions for selected tag
			this.fetchReactions().
				then(() => {
					this.fetchQuestionsCollection(this.quizQuestionsIds)
				})
		}
	}
</script>
