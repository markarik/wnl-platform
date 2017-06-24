<template>
	<div class="scrollable-main-container" ref="overviewContainer">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item metadata">
					Cześć {{currentUserName}}!
				</div>
			</div>
		</div>

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
		<wnl-qna title="Ostatnie pytania"></wnl-qna>
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
</style>

<script>
	import emoji from 'node-emoji'
	import { mapGetters } from 'vuex'

	import NextLesson from 'js/components/course/NextLesson'
	import Qna from 'js/components/qna/Qna'
	import YourProgress from 'js/components/course/YourProgress'
	import ActiveUsers from 'js/components/course/ActiveUsers'
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
			'wnl-next-lesson': NextLesson,
			'wnl-qna': Qna,
			'wnl-your-progress': YourProgress,
			'wnl-active-users': ActiveUsers
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
		}
	}
</script>
