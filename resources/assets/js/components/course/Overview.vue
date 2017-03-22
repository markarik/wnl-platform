<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ welcomeMessage }}
				</div>
			</div>
			<div class="level-right">

			</div>
		</div>
		<div class="wnl-overview-section">
			<wnl-next-lesson :courseId="courseId"></wnl-next-lesson>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Jak Ci idzie?
				</div>
			</div>
		</div>
		<div class="wnl-overview-section">
			<wnl-your-progress :courseId="courseId"></wnl-your-progress>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-overview-section
		margin-bottom: $margin-big
</style>

<script>
	import NextLesson from 'js/components/course/NextLesson.vue'
	import YourProgress from 'js/components/course/YourProgress.vue'
	import { mapGetters } from 'vuex'

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters([
				'currentUserName',
			]),
			welcomeMessage() {
				return `Cześć ${this.currentUserName}!`
			},
		},
		components: {
			'wnl-next-lesson': NextLesson,
			'wnl-your-progress': YourProgress,
		},
	}
</script>
