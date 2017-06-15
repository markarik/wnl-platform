<template>
	<div class="scrollable-main-container" :style="{height: `${elementHeight}px`}">
	<!-- <div> -->
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
			<div class="wnl-lesson-previous-next-nav">
				<wnl-previous-next></wnl-previous-next>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	$previous-next-height: 45px

	.wnl-lesson
		width: 100%

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
	import Breadcrumbs from 'js/components/global/Breadcrumbs'
	import PreviousNext from 'js/components/course/PreviousNext'
	import { mapGetters, mapActions } from 'vuex'
	import { resource } from 'js/utils/config'
	import { breadcrumb } from 'js/mixins/breadcrumb'
	import Qna from 'js/components/qna/Qna.vue'
	import {STATUS_COMPLETE} from '../../services/progressStore';

	export default {
		name: 'Lesson',
		components: {
			'wnl-previous-next': PreviousNext,
			'wnl-breadcrumbs': Breadcrumbs,
		},
		mixins: [ breadcrumb ],
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
				'getScreenSectionsCheckpoints',
			]),
			...mapGetters('progress', [
				'getSavedLesson',
				'shouldCompleteLesson',
				'shouldCompleteScreen'
			]),
			breadcrumb() {
				return {
					text: this.lessonName,
					to: {
						name: 'lessons',
						params: {
							courseId: this.courseId,
							lessonId: this.lessonId,
						}
					}
				}
			},
			lessonName() {
				return this.getLesson(this.lessonId).name
			},
			screens() {
				return this.getScreens(this.lessonId)
			},
			currentScreen() {
				return this.getScreen(this.screenId);
			},
			sectionsReversed() {
				const sectionsIds = this.currentScreen.sections;

				if (!sectionsIds) {
					return;
				}

				const sections = this.getSections(sectionsIds);
				return sections.map((section) => section).reverse();
			},
			hasSections() {
				return !!this.sectionsReversed;
			},
			currentSection() {
				return this.sectionsReversed.find((section) => this.slide >= section.slide);
			},
			firstScreenId() {
				if (_.isEmpty(this.screens)) {
					return null
				}

				return _.head(this.screens).id
			},
			lessonProgressContext() {
				return {
					courseId: this.courseId,
					lessonId: this.lessonId,
					screenId: this.screenId,
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
				'completeScreen',
				'completeSection'
			]),
			launchLesson() {
				this.startLesson(this.lessonProgressContext).then(() => {
					this.goToDefaultScreenIfNone()
				});
			},
			goToDefaultScreenIfNone() {
				if (!this.screenId) {
					this.getSavedLesson(this.courseId, this.lessonId)
						.then(({route, status}) => {
							if (this.firstScreenId && (!route || status === STATUS_COMPLETE || route && route.name !== resource('screens'))) {
								const params = {
									courseId: this.courseId,
									lessonId: this.lessonId,
									screenId: this.firstScreenId,
								};
								if (this.getScreen(this.firstScreenId).type === 'slideshow' && !_.get(route, 'params.slide')) {
									params.slide = 1;
								}
								this.$router.replace({name: resource('screens'), params})
							} else if (route && route.hasOwnProperty('name')) {
								this.$router.replace(route)
							}
						});
				} else if (this.screenId && !this.slide) {
					const params = {
						courseId: this.courseId,
						lessonId: this.lessonId,
						screenId: this.screenId,
					};

					if (this.currentScreen.type === 'slideshow') {
						params.slide = 1;
					}
					this.$router.replace({name: resource('screens'), params})
				}
			},
			updateLessonProgress() {
				if (typeof this.screenId !== 'undefined') {
					if (this.hasSections && this.currentSection) {
						if (this.getScreenSectionsCheckpoints(this.screenId).includes(this.slide)) {
							this.completeSection({...this.lessonProgressContext, sectionId: this.currentSection.id})
						}
					}

					if (this.shouldCompleteScreen(this.courseId, this.lessonId, this.screenId)) {
						this.completeScreen(this.lessonProgressContext);

						if (this.shouldCompleteLesson(this.courseId, this.lessonId)) {
							this.completeLesson(this.lessonProgressContext)
						}
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
			'$route' () {
				this.goToDefaultScreenIfNone()
				this.updateLessonProgress()
			}
		},
	}
</script>
