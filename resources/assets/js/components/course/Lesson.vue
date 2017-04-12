<template>
	<div class="wnl-lesson">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item metadata">
					{{lessonName}}
				</div>
			</div>
			<div class="level-right">
				<div class="level-item small">
					Lekcja {{lessonId}}
				</div>
			</div>
		</div>
		<keep-alive>
			<router-view></router-view>
		</keep-alive>
		<!-- <wnl-qna :lessonId="lessonId"></wnl-qna> -->
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-title
		padding-bottom: $margin-base
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import { resource } from 'js/utils/config'
	import  Qna from './../qna/Qna.vue'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId'],
		computed: {
			...mapGetters([
				'getScreens',
				'progressGetSavedLesson',
				'getLesson',
			]),
			lessonName() {
				return this.getLesson(this.lessonId).name
			},
			firstScreenId() {
				if (typeof this.getScreens(this.lessonId) !== 'undefined') {
					return parseInt(this.getScreens(this.lessonId)[0])
				}
			},
			lastScreenId() {
				if (typeof this.getScreens(this.lessonId) !== 'undefined') {
					return parseInt(this.getScreens(this.lessonId).slice(-1)[0])
				}
			},
			lessonProgressContext() {
				return {
					courseId: this.courseId,
					lessonId: this.lessonId,
					route: {
						name: this.$route.name,
						params: this.$route.params,
						query: this.$route.query,
						meta: this.$route.meta,
					},
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
				this.progressStartLesson(this.lessonProgressContext)
				this.goToDefaultScreenIfNone()
			},
			goToDefaultScreenIfNone() {
				if (!this.screenId) {
					let savedRoute = this.progressGetSavedLesson(this.courseId, this.lessonId)
					if (typeof savedRoute !== 'undefined' && savedRoute.hasOwnProperty('name')) {
						this.$router.replace(savedRoute)
					} else if (typeof this.firstScreenId !== 'undefined') {
						this.$router.replace({ name: resource('screens'), params: {
							courseId: this.courseId,
							lessonId: this.lessonId,
							screenId: this.firstScreenId,
						} })
					}
				}
			},
			updateLessonProgress() {
				if (typeof this.screenId !== 'undefined') {
					if (parseInt(this.screenId) === this.lastScreenId) {
						this.progressCompleteLesson(this.lessonProgressContext)
					} else {
						this.progressUpdateLesson(this.lessonProgressContext)
					}
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
		},
		components: {
			'wnl-qna': Qna,
		},
	}
</script>
