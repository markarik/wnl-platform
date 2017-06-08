<template>
	<div class="scrollable-main-container" :style="{height: `${elementHeight}px`}">
		<div class="wnl-lesson">
			<div class="wnl-lesson-view">
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
				<router-view></router-view>
			</div>
		</div>
		<div class="wnl-lesson-previous-next-nav">
			<wnl-previous-next></wnl-previous-next>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	$previous-next-height: 45px

	.wnl-lesson-view
		padding-bottom: calc(2 * #{$previous-next-height})

	.wnl-lesson-previous-next-nav
		background: $color-white
		bottom: 0
		height: $previous-next-height
		left: 0
		right: 0
		position: absolute

	.wnl-screen-title
		padding-bottom: $margin-base
</style>

<script>
	import _ from 'lodash'
	import Qna from 'js/components/qna/Qna.vue'
	import PreviousNext from 'js/components/course/PreviousNext.vue'
	import {mapGetters, mapActions} from 'vuex'
	import {resource} from 'js/utils/config'

	export default {
		name: 'Lesson',
		components: {
			'wnl-previous-next': PreviousNext,
		},
		props: ['courseId', 'lessonId', 'screenId'],
		data() {
			return {
				/**
				 * elementHeight is used to prevent Safari from expanding
				 * a container with an overflow-y: auto and height: 100%.
				 * Using a specific height, the height of the parent element
				 * (which btw is defined as 100% of its parent element),
				 * all browsers are able to beautifully scroll the content.
				 */
				elementHeight: this.$parent.$el.offsetHeight
			}
		},
		computed: {
			...mapGetters('course', [
				'getScreens',
				'getLesson',
			]),
			...mapGetters('progress', [
				'getSavedLesson'
			]),
			lessonName() {
				return this.getLesson(this.lessonId).name
			},
			screens() {
				return this.getScreens(this.lessonId)
			},
			firstScreenId() {
				if (_.isEmpty(this.screens)) {
					return null
				}

				return _.head(this.screens).id
			},
			lastScreenId() {
				if (_.isEmpty(this.screens)) {
					return null
				}

				return _.last(this.screens).id
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
			...mapActions('progress', [
				'startLesson',
				'updateLesson',
				'completeLesson',
			]),
			launchLesson() {
				this.startLesson(this.lessonProgressContext)
				this.goToDefaultScreenIfNone()
			},
			goToDefaultScreenIfNone() {
				if (!this.screenId) {
					let savedRoute = this.getSavedLesson(this.courseId, this.lessonId)
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
						this.completeLesson(this.lessonProgressContext)
					} else {
						this.updateLesson(this.lessonProgressContext)
					}
				}
			},
			updateElementHeight() {
				this.elementHeight = this.$parent.$el.offsetHeight
			},
		},
		mounted () {
			this.launchLesson()
			window.addEventListener('resize', this.updateElementHeight)
		},
		beforeDestroy () {
			window.removeEventListener('resize', this.updateElementHeight)
		},
		watch: {
			'$route' (to, from) {
				this.goToDefaultScreenIfNone()
				this.updateLessonProgress()
			}
		},
	}
</script>
