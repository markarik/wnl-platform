<template>
	<div class="nextLesson box">
		<div class="level">
			<div class="level-left">
				<div class="level-item caption">
					{{ heading }}
				</div>
			</div>
		</div>
		<div class="lesson">
			<p class="group">{{ groupName }}</p>
			<p class="name">{{ lessonName }}</p>
			<router-link :to="to" class="button" :class="buttonClass" v-if="hasNextLesson">{{ callToAction }}</router-link>
			<div v-else>Otwiera się {{ nextLessonDate }}</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.lesson
		text-align: center

	.group
		text-transform: uppercase

	.name
		font-size: $font-size-plus-6
		line-height: $line-height-plus
		margin-bottom: 0.5em
</style>

<script>
	import { mapGetters } from 'vuex'

	import Emoji from '../global/Emoji.vue'
	import { getUrl } from 'js/utils/env'
	import { resource } from 'js/utils/config'
	import { timeFromDate } from 'js/utils/time'

	const STATUS_NONE = 'none'
	const STATUS_IN_PROGRESS = 'in-progress'
	const STATUS_AVAILABLE = 'available'

	const statusParams = {
		[STATUS_NONE]: {
			heading: 'Najbliższa lekcja',
		},
		[STATUS_IN_PROGRESS]: {
			heading: 'Lekcja w trakcie',
			callToAction: 'Wróć do lekcji',
			buttonClass: 'is-primary is-outlined',
		},
		[STATUS_AVAILABLE]: {
			heading: 'Następna lekcja',
			callToAction: 'Rozpocznij lekcję',
			buttonClass: 'is-primary',
		},
	}

	export default {
		name: 'NextLesson',
		props: ['courseId'],
		computed: {
			...mapGetters('course', [
				'getGroup',
				'getLessons',
				'getLesson',
				'isLessonAvailable',
			]),
			...mapGetters('progress', [
				'wasLessonStarted',
				'getFirstLessonIdInProgress',
				'isLessonComplete',
			]),
			nextLesson() {
				let lesson = { status: STATUS_NONE },
					inProgressId = this.getFirstLessonIdInProgress(this.courseId)

				if (inProgressId > 0) {
					lesson = this.getLesson(inProgressId)
					lesson.status = STATUS_IN_PROGRESS
				} else {
					for (var lessonId in this.getLessons) {
						let isAvailable = this.isLessonAvailable(lessonId)
						if (isAvailable &&
							!this.wasLessonStarted(this.courseId, lessonId)
						) {
							lesson = this.getLesson(lessonId)
							lesson.status = STATUS_AVAILABLE
							break
						} else if (!isAvailable) {
							lesson = this.getLesson(lessonId)
							lesson.status = STATUS_NONE
							break
						}
					}
				}

				return lesson
			},
			hasNextLesson() {
				return this.nextLesson.status !== STATUS_NONE
			},
			heading() {
				return this.getParam('heading')
			},
			callToAction() {
				return this.getParam('callToAction')
			},
			buttonClass() {
				return this.getParam('buttonClass')
			},
			groupName() {
				return this.getGroup(this.nextLesson.groups).name
			},
			lessonName() {
				return this.nextLesson.name
			},
			nextLessonDate() {
				return timeFromDate(this.nextLesson.startDate.date)
			},
			to() {
				return {
					name: resource('lessons'),
					params: {
						courseId: this.courseId,
						lessonId: this.nextLesson.id,
					}
				}
			},
		},
		methods: {
			getParam(name) {
				return statusParams[this.nextLesson.status][name]
			}
		},
		components: {
			'wnl-emoji': Emoji
		}
	}
</script>
