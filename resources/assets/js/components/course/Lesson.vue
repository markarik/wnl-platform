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
		<div class="level">
			<div class="level-left">
				<div class="level-item" v-if="previousScreenRoute !== undefined">
					<router-link :to="previousScreenRoute">Poprzedni</router-link>
				</div>
			</div>
			<div class="level-right">
				<div class="level-item" v-if="nextScreenRoute !== undefined">
					<router-link :to="nextScreenRoute">Następny</router-link>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-title
		padding-bottom: $margin-base
</style>

<script>
	import _ from 'lodash'
	import  Qna from './../qna/Qna.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId'],
		computed: {
			...mapGetters('course', [
				'getScreens',
				'getLesson',
				'getAdjacentScreenId',
			]),
			...mapGetters('progress', [
				'getSavedLesson'
			]),
			lessonName() {
				return this.getLesson(this.lessonId).name
			},
			firstScreenId() {
				if (typeof this.getScreens(this.lessonId) !== 'undefined') {
					return parseInt(this.getScreens(this.lessonId)[0])
				}
			},
			previousScreenRoute() {
				return this.getAdjacentScreenRoute('previous')
			},
			nextScreenRoute() {
				return this.getAdjacentScreenRoute('next')
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
			getAdjacentScreenRoute(direction) {
				let id = this.getAdjacentScreenId(this.lessonId, this.screenId, direction)

				if (id === undefined) {
					return undefined
				} else {
					return {
						name: resource('screens'),
						params: {
							courseId: this.courseId,
							lessonId: this.lessonId,
							screenId: id,
						}
					}
				}
			},
		},
		mounted () {
			this.launchLesson()
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
