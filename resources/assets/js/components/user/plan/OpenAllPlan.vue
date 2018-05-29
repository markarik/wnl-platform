<template>
	<div>
		<div class="wnl-overlay" v-if="isLoading">
			<span class="loader"></span>
			<span class="loader-text">{{ $t('lessonsAvailability.loader') }}</span>
		</div>
		<div class="open-all">
			<div class="level">
				{{ $t('lessonsAvailability.openAllLessons.annotation') }}
			</div>
			<div class="level">
				{{ $t('lessonsAvailability.openAllLessons.paragraphAnnotation')}}
				{{ this.completedLessonsLength }}/{{ this.availableLength }}.
				wyświetli się: {{ this.completedLessonsLength }}/{{this.requiredLength}}.
			</div>
			<span>{{ $t('lessonsAvailability.openAllLessons.paragraphExplanation')}}</span>
		</div>
		<div class="accept-plan">
			<a
				@click="acceptPlan"
				class="button button is-primary is-outlined is-big"
			>{{ $t('lessonsAvailability.buttons.acceptPlan') }}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-overlay
		align-items: center
		background: rgba(255, 255, 255, 0.9)
		bottom: 0
		display: flex
		flex-direction: column
		justify-content: center
		left: 0
		position: fixed
		right: 0
		top: 0
		z-index: $z-index-overlay

		.loader
			height: 40px
			width: 40px

		.loader-text
			color: $color-ocean-blue
			margin-top: $margin-small

	.open-all
		margin-bottom: $margin-base
		width: 100%
		overflow-wrap: wrap
		.level-item
			width: 100%

	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'
	import { getApiUrl } from 'js/utils/env'
	import momentTimezone from 'moment-timezone'

	export default {
		name: 'OpenAllPlan',
		data() {
			return {
				isLoading: false,
				alertSuccess: {
					text: this.$i18n.t('lessonsAvailability.alertSuccess'),
					type: 'success',
				},
				alertError: {
					text: this.$i18n.t('lessonsAvailability.alertError'),
					type: 'error',
				},
			}
		},
		computed: {
			...mapGetters('course', [
				'getRequiredLessons',
				'userLessons',
			]),
			...mapGetters('progress', ['getCompleteLessons']),
			...mapGetters(['currentUserId']),
			availableLength() {
				return this.userLessons.filter(lesson => lesson.isAvailable && lesson.is_required).length
			},
			requiredLength() {
				return this.userLessons.filter(lesson => lesson.is_required).length
			},
			inProgressLessonsLength() {
				return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
					return !this.completedLessons.includes(Number(requiredLesson))
				}).length
			},
			completedLessonsLength() {
				return Object.keys(this.getRequiredLessons).filter(requiredLesson => {
					return this.completedLessons.includes(Number(requiredLesson))
				}).length
			},
			completedLessons() {
				return this.getCompleteLessons(1).map(lesson => lesson.id)
			},
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			...mapActions('course', ['setStructure']),
			async acceptPlan() {
				this.isLoading = true
				try {
					const response = await axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
						work_load: 0,
						timezone: momentTimezone.tz.guess(),
						preset_active: 'openAll',
					})
					await this.setStructure()
					this.addAutoDismissableAlert(this.alertSuccess)
					this.isLoading = false
				}
				catch(error) {
					this.isLoading = false
					$wnl.logger.capture(error)
					this.addAutoDismissableAlert(this.alertError)
				}
			}
		}
	}
</script>
