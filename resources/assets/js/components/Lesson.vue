<template>
	<div class="wnl-app-layout">
		<div class="wnl-app-layout-left">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="wnl-app-layout-main">
			<router-view></router-view>
		</div>
		<div class="wnl-app-layout-right">
			<wnl-chat :room="chatRoom"></wnl-chat>
		</div>
	</div>
</template>

<style>

</style>

<script>
	import Sidenav from './Sidenav.vue'
	import Chat from './chat/Chat.vue'
	import { mapGetters, mapActions, mapMutations } from 'vuex'
	import { getApiUrl } from '../utils/env'
	import * as mutations from 'js/store/mutations-types'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			...mapGetters([
				'firstItem'
			]),
			chatRoom() {
				return `courses-${this.courseId}-lessons-${this.lessonId}`
			},
			navigationApiUrl() {
				return getApiUrl(`lessons/${this.lessonId}/nav`)
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-chat': Chat
		},
		methods: {
			...mapMutations([
				mutations.PROGRESS_START_LESSON,
				mutations.PROGRESS_UPDATE_LESSON,
				mutations.PROGRESS_COMPLETE_LESSON
			]),
			...mapActions([
				'progressSetupEdition',
				'progressStartLesson'
			]),
			startLesson() {
				this.progressStartLesson({
					editionId: this.courseId,
					lessonId: this.lessonId
				})
				this.goToFirstScreenByDefault()
			},
			goToFirstScreenByDefault() {
				if (!this.screenId) {
					this.$router.replace({ name: 'screens', params: { screenId: this.firstItem.id } })
				}
			}
		},
		created() {
			if (!this.wasProgressChecked) {
				this.progressSetupEdition(this.courseId).then()
			}
		},
		mounted () {
			this.startLesson()
		},
		watch: {
			'$route' (to, from) { this.goToFirstScreenByDefault() }
		}
	}
</script>
