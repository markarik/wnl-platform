<template>
	<div class="scrollable-main-container" ref="overviewContainer">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item metadata">
					Cześć {{currentUserName}}!
				</div>
			</div>
		</div>

		<!-- Dashboard news -->
		<wnl-dashboard-news></wnl-dashboard-news>

		<!-- Next lesson -->
		<div>
			<div class="wnl-overview-section">
				<wnl-next-lesson :courseId="courseId"></wnl-next-lesson>
			</div>
		</div>

		<!-- Your progress -->
		<div>
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item metadata">
						Jak Ci idzie?
					</div>
				</div>
			</div>
			<div class="wnl-overview-section">
				<wnl-your-progress :courseId="courseId"></wnl-your-progress>
			</div>
		</div>

		<wnl-active-users/>
		<!-- Latest Q&A -->
		<wnl-qna title="Ostatnie pytania" class="wnl-overview-qna"></wnl-qna>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.content
		color: $color-gray

	.wnl-overview-section
		margin-bottom: $margin-big

	.wnl-overview
		padding-bottom: 20em

	.wnl-overview-qna
		margin: $margin-huge 0
</style>

<script>
	import emoji from 'node-emoji'
	import { mapGetters, mapActions } from 'vuex'

	import Qna from 'js/components/qna/Qna'
	import ActiveUsers from 'js/components/course/dashboard/ActiveUsers'
	import DashboardNews from 'js/components/course/dashboard/DashboardNews'
	import NextLesson from 'js/components/course/dashboard/NextLesson'
	import YourProgress from 'js/components/course/dashboard/YourProgress'
	import { getFirstLessonId } from 'js/utils/env'
	import { resource } from 'js/utils/config'

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters('progress', [
				'isLessonComplete',
				'wasCourseStarted',
			]),
			...mapGetters([
				'currentUserName',
			]),
			isBeginning() {
				return !this.wasCourseStarted(this.courseId)
			},
		},
		components: {
			'wnl-qna': Qna,
			'wnl-active-users': ActiveUsers,
			'wnl-dashboard-news': DashboardNews,
			'wnl-next-lesson': NextLesson,
			'wnl-your-progress': YourProgress,
		},
		methods: {
			...mapActions('qna', ['fetchLatestQuestions'])
		},
		beforeMount() {
			if (this.isBeginning) {
				this.$router.replace({
					name: resource('lessons'),
					params: {
						lessonId: getFirstLessonId(),
						courseId: this.courseId
					}
				})
			}
		},
		mounted() {
			this.fetchLatestQuestions()
		}
	}
</script>
