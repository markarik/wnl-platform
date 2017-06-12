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
	import {STATUS_COMPLETE} from '../../store/modules/progress';

	export default {
		name: 'Lesson',
		components: {
			'wnl-previous-next': PreviousNext,
		},
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
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
				'getSections',
				'getScreen',
				'getScreenSectionsCheckpoints'
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
			currentScreen() {
				return this.getScreen(this.screenId);
			},
			currentSection() {
				const sectionsIds = this.currentScreen.sections;

				if (!sectionsIds) {
					return;
				}

				const sections = this.getSections(sectionsIds);
				const sectionsReversed = sections.map((section) => section).reverse();

				return sectionsReversed.find((section) => this.slide >= section.slide);

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
					screenId: this.screenId,
					section: this.section,
					slide: this.slide,
					route: {
						name: this.$route.name,
						params: this.$route.params,
						query: this.$route.query,
						meta: this.$route.meta,
					},
				}
			},
			lastSectionSlideId() {
				return _.last(this.getScreenSectionsCheckpoints(this.screenId));
			}
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
					this.getSavedLesson(this.courseId, this.lessonId)
						.then(({route, status}) => {
							if (typeof this.firstScreenId !== 'undefined' && status === STATUS_COMPLETE) {
								this.$router.replace({
									name: resource('screens'), params: {
										courseId: this.courseId,
										lessonId: this.lessonId,
										screenId: this.firstScreenId,
									}
								})
							} else if (typeof route !== 'undefined' && route.hasOwnProperty('name')) {
								this.$router.replace(route)
							}
						});
				}
			},
			updateLessonProgress() {
				if (typeof this.screenId !== 'undefined') {
					// TODO
					// check if section changed -> if yes change section
					// check if last section -> mark screen as completed
					// check if last screen -> mark lesson as completed

					// TODO get section for slide
					console.log('************', this.currentSection);

					// TODO below works only for screens with slides - fix for rest
					if (parseInt(this.slide) >= this.lastSectionSlideId) {
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
