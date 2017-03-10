<template>
	<div class="wnl-lesson">
		<keep-alive>
			<router-view></router-view>
		</keep-alive>
	</div>
</template>

<script>
	import { mapGetters } from 'vuex'
	// import * as mutations from 'js/store/mutations-types'
	import { resource } from 'js/utils/config'

	export default {
		name: 'Lesson',
		props: ['lessonId', 'screenId'],
		computed: {
			...mapGetters([
				'getFirstScreen'
			])
		},
		methods: {
			// ...mapMutations([
			// 	mutations.PROGRESS_START_LESSON,
			// 	mutations.PROGRESS_UPDATE_LESSON,
			// 	mutations.PROGRESS_COMPLETE_LESSON
			// ]),
			// ...mapActions([
			// 	'progressSetupEdition',
			// 	'progressStartLesson'
			// ]),
			// startLesson() {
			// 	this.progressStartLesson({
			// 		editionId: this.courseId,
			// 		lessonId: this.lessonId
			// 	})
			// 	this.goToFirstScreenByDefault()
			// },
			goToFirstScreenByDefault() {
				if (!this.screenId) {
					let firstScreen = this.getFirstScreen(this.lessonId)
					this.$router.replace({ name: resource('screens'), params: { screenId: firstScreen.id } })
				}
			}
		},
		created() {
			// if (!this.wasProgressChecked) {
			// 	this.progressSetupEdition(this.courseId).then()
			// }
		},
		mounted () {
			// this.startLesson()
			this.goToFirstScreenByDefault()
		},
		watch: {
			'$route' (to, from) { this.goToFirstScreenByDefault() }
		}
	}
</script>
