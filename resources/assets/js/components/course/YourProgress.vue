<template>
	<div class="your-progress">
		<p class="progress-message big">{{ progressMessage }}</p>
		<wnl-progress
			:value="progressValue"
			:max="progressMax"
			:hasNumbers="progressHasNumbers"
			:modifyingClass="progressModifyingClass">
		</wnl-progress>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.progress-message
		margin: 0 0 $margin-base
</style>

<script>
	import emoji from 'node-emoji'
	import Progress from 'js/components/global/Progress.vue'
	import { mapGetters } from 'vuex'

	const STATE_FULL = 'full',
		STATE_GOOD = 'good',
		STATE_WARNING = 'warning',
		STATE_DANGER = 'danger',
		stateData = {
			[STATE_FULL]: {
				message: `Świetnie Ci idzie! Wszystkie dostępne lekcje są już zakończone. Należy Ci się zasłużony odpoczynek. ${emoji.get('slightly_smiling_face')}`,
				modifyingClass: 'is-success'
			},
			[STATE_GOOD]: {
				message: 'Jesteś na dobrej drodze! Tak trzymaj, a spokojnie się wyrobisz. :)',
				modifyingClass: 'is-success'
			},
			[STATE_WARNING]: {
				message: 'Hmmm, chyba warto odrobinę przyspieszyć, aby nie zostać w tyle!',
				modifyingClass: 'is-warning'
			},
			[STATE_DANGER]: {
				message: 'O-o, trzeba się sprężać! Jeśli potrzebujesz pomocy, napisz na pomocy@wiecejnizlek.pl. :)',
				modifyingClass: 'is-danger'
			},
		}

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters('course', [
				'getAvailableLessons',
			]),
			...mapGetters(['progressGetCompleteLessons']),
			progressValue() {
				return this.progressGetCompleteLessons(this.courseId).length
			},
			progressMax() {
				return this.getAvailableLessons.length
			},
			progressState() {
				const incompleteLessons = this.progressMax - this.progressValue

				if (incompleteLessons === 0) {
					return 'full'
				} else if (incompleteLessons < 4) {
					return 'good'
				} else if (incompleteLessons < 8) {
					return 'warning'
				}
				return 'danger'
			},
			progressHasNumbers() {
				return true
			},
			progressModifyingClass() {
				return stateData[this.progressState].modifyingClass
			},
			progressMessage() {
				return stateData[this.progressState].message
			},
		},
		components: {
			'wnl-progress': Progress,
		},
	}
</script>
