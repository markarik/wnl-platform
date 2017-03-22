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
		<div class="wnl-screen-content">
			<wnl-next-lesson :courseId="courseId" lessonId="1"></wnl-next-lesson>
		</div>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import NextLesson from 'js/components/course/NextLesson.vue'
	import { mapGetters } from 'vuex'

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters(['currentUserName']),
			welcomeMessage() {				
				return `Cześć ${this.currentUserName}!`
			},
		},
		components: {
			'wnl-next-lesson': NextLesson,
		},
	}
</script>
