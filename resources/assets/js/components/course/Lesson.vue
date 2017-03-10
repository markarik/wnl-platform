<template>
	<div class="wnl-lesson">
		<keep-alive>
			<router-view></router-view>
		</keep-alive>
	</div>
</template>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId'],
		computed: {
			...mapGetters([
				'getScreens',
				'progressGetSavedLesson',
			]),
			lessonProgressContext() {
				return {
					courseId: this.courseId,
					lessonId: this.lessonId,
					route: this.$route,
				}
			},
		},
		methods: {
			...mapActions([
				'progressStartLesson',
				'progressUpdateLesson',
				'progressCompleteLesson',
			]),
			startLesson() {
				console.log(`Starting lesson ${this.lessonId}`)
				this.progressStartLesson(this.lessonProgressContext)
				this.goToDefaultScreenIfNone()
			},
			goToDefaultScreenIfNone() {
				if (!this.screenId) {
					let savedRoute = this.progressGetSavedLesson(this.courseId, this.lessonId)

					if (typeof savedRoute !== 'undefined' && savedRoute.hasOwnProperty('name')) {
						console.log(savedRoute)
						this.$router.replace(savedRoute)
					} else {
						let firstScreen = this.getScreens(this.lessonId)[0]

						this.$router.replace({ name: resource('screens'), params: { screenId: firstScreen.id } })
					}
				}
			},
			updateLessonProgress() {
				if (typeof this.screenId !== 'undefined') {
					let lastScreen = this.getScreens(this.lessonId).slice(-1)[0]

					if (this.screenId === lastScreen.id) {
						this.progressCompleteLesson(this.lessonProgressContext)
					}
					this.progressUpdateLesson(this.lessonProgressContext)
				}
			},
		},
		mounted () {
			this.startLesson()
		},
		watch: {
			'$route' (to, from) {
				this.goToDefaultScreenIfNone()
				this.updateLessonProgress()
			}
		}
	}
</script>
