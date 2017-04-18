<template>
	<div class="nextLesson box">
		<div class="level">
			<div class="level-left">
				<div class="level-item caption">
					{{ heading }}
				</div>
			</div>
		</div>
		<div class="lesson" v-if="hasNextLesson">
			<p class="group">{{ groupName }}</p>
			<p class="name">{{ lessonName }}</p>
			<router-link :to="to" class="button" :class="buttonClass">{{ callToAction }}</router-link>
		</div>
		<div v-else>
			<!-- TODO: Mar 22, 2017 - Obviously, we have to fix it to dynamically calculate availability (a nie "będzie dostępny jutro") -->
			<p class="strong margin vertical has-text-centered">
				<span class="margin horizontal"><wnl-emoji name="tada"></wnl-emoji></span>
					{{ callToAction }}
				<span class="margin horizontal"><wnl-emoji name="tada"></wnl-emoji></span>
				<p class="has-text-centered margin vertical">
					Teraz pozostało się już tylko zapisać <wnl-emoji name="rocket"></wnl-emoji>
				</p>
				<p class="has-text-centered margin vertical">
					<a :href="paymentUrl" class="button is-primary">
						Zapisz się
					</a>
				</p>
			</p>
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
	import Emoji from '../global/Emoji.vue'
	import { getUrl } from 'js/utils/env'
	import { mapGetters } from 'vuex'
	import { resource } from 'js/utils/config'

	const STATUS_NONE = 'none'
	const STATUS_IN_PROGRESS = 'in-progress'
	const STATUS_AVAILABLE = 'available'

	const statusParams = {
		[STATUS_NONE]: {
			heading: 'Gratulacje!',
			callToAction: `To już koniec naszego kursu!`,
			buttonClass: '',
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
			]),
			nextLesson() {
				let lesson = { status: STATUS_NONE },
					inProgressId = this.getFirstLessonIdInProgress(this.courseId)

				if (this.progressIsLessonComplete(this.courseId, 3)) {
					return lesson
				}

				if (inProgressId > 0) {
					lesson = this.getLesson(inProgressId)
					lesson.status = STATUS_IN_PROGRESS
				} else {
					for (const lessonId in this.getLessons) {
						if (this.isLessonAvailable(lessonId) &&
							!this.wasLessonStarted(this.courseId, lessonId)
						) {
							lesson = this.getLesson(lessonId)
							lesson.status = STATUS_AVAILABLE
							break
						}
					}
				}

				return lesson
			},
			hasNextLesson() {
				return !this.progressIsLessonComplete(this.courseId, 3) &&
					this.nextLesson.status !== STATUS_NONE
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
			to() {
				return {
					name: resource('lessons'),
					params: {
						courseId: this.courseId,
						lessonId: this.nextLesson.id,
					}
				}
			},
			paymentUrl() {
				// return getUrl('payment/select-product')
				return 'https://platforma.wiecejnizlek.pl/payment/select-product'
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
