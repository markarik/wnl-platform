<template>
	<div class="next-lesson">
		<div class="next">{{ next }}</div>
		<div>
			<span class="group">{{ groupName }} <span class="icon is-small"><i class="fa fa-angle-right"></i></span> </span>
			<span class="lesson">{{ lessonName }}</span>
		</div>
		<div class="cta">
			<router-link v-if="hasNextLesson"
				class="button is-primary"
				:class="{'is-outlined': status === 'in-progress'}"
				:to="to"
			>
				{{ callToAction }}
			</router-link>
			<span class="text" v-else>{{ $t('dashboard.progress.none-CTA', {date: nextLessonDate}) }}</span>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.next-lesson
		margin-bottom: $margin-big

		div
			text-align: center

	.next
		font-weight: $font-weight-bold
		margin-bottom: $margin-medium
		text-transform: uppercase

	.group
		font-size: $font-size-minus-1
		letter-spacing: 1px
		text-transform: uppercase

	.lesson
		font-size: $font-size-plus-1


	.cta
		font-size: $font-size-minus-1

		.button
			margin-top: $margin-medium
			font-size: $font-size-minus-2

		.text
			color: $color-gray-dimmed

</style>

<script>
	import { truncate } from 'lodash'
	import { mapGetters } from 'vuex'

	import { getUrl } from 'js/utils/env'
	import { resource } from 'js/utils/config'
	import { timeFromDate } from 'js/utils/time'

	const STATUS_NONE = 'none'
	const STATUS_IN_PROGRESS = 'in-progress'
	const STATUS_AVAILABLE = 'available'

	export default {
		name: 'NextLesson',
		computed: {
			...mapGetters('course', [
				'getGroup',
				'getLessons',
				'getLesson',
				'isLessonAvailable',
				'nextLesson'
			]),
			...mapGetters('progress', [
				'wasLessonStarted',
				'getFirstLessonIdInProgress',
				'isLessonComplete',
			]),
			buttonClass() {
				return this.getParam('buttonClass')
			},
			callToAction() {
				return this.$t(`dashboard.progress.${this.status}-CTA`)
			},
			courseId() {
				return this.$route.params.courseId
			},
			groupName() {
				return this.getGroup(this.nextLesson.groups).name
			},
			hasNextLesson() {
				return this.nextLesson && this.status !== STATUS_NONE
			},
			lessonName() {
				return truncate(this.nextLesson.name, {length: 30})
			},
			next() {
				return this.$t(`dashboard.progress.${this.status}`)
			},
			nextLessonDate() {
				console.log(timeFromDate(this.nextLesson.startDate.date), this.nextLesson.startDate.date, '********')
				return timeFromDate(this.nextLesson.startDate.date)
			},
			status() {
				return this.nextLesson.status
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
				return statusParams[this.status][name]
			}
		},
	}
</script>
