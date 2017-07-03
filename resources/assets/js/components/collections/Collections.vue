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
		props: ['courseId', 'lessonId'],
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
			...mapGetters('course', ['groups','structure'])
		},
		methods: {
			...mapActions('collections', ['fetchReactions']),
			...mapActions('quiz', ['fetchQuestionsCollection']),
			...mapActions('course', ['setStructure']),
			getNavigation() {
				if (this.isStructureEmpty) {
					$wnl.logger.debug('Empty structure, WTF?')
					$wnl.logger.debug(this.structure)
					return
				}

				let navigation = [];

				this.groups.forEach((groupId) => {
					const group = this.structure.groups[groupId]

					const groupNavigation = [
						this.getGroupItem(group),
						...group.lessons.map((lessonId) => {
							const lesson = this.structure.lessons[lessonId]
							return this.getLessonItem(lesson);
						})
					]

					navigation = [...navigation, ...groupNavigation];
				});

				return navigation
			},
			getGroupItem(group) {
				return navigation.composeItem({
					text: group.name,
					itemClass: 'heading small'
				})
			},
			getLessonItem(lesson, withProgress = true) {
				return navigation.composeItem({
					text: lesson.name,
					itemClass: 'has-icon',
					routeName: 'collections-lesson',
					routeParams: {
						lessonId: lesson.id,
					},
					iconClass: 'fa-graduation-cap',
					iconTitle: 'Obecna lekcja'
				})
			},
		},

		mounted() {
			this.setStructure(this.courseId)
				.then(this.fetchReactions)
				.then(() => this.fetchQuestionsCollection(this.quizQuestionsIds))
		}
	}
</script>
