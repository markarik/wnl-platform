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
	import { mapGetters } from 'vuex'
	import { getApiUrl } from '../utils/env'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			chatRoom() {
				return `courses-${this.courseId}-lessons-${this.lessonId}`
			},
			navigationApiUrl() {
				return getApiUrl(`lessons/${this.lessonId}/nav`)
			},
			...mapGetters([
				'firstItem'
			])
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-chat': Chat
		},
		methods: {
			goToFirstScreenByDefault() {
				if (!this.screenId) {
					this.$router.replace({ name: 'screens', params: { screenId: this.firstItem.id } })
				}
			}
		},
		mounted () { this.goToFirstScreenByDefault() },
		watch: {
			'$route' (to, from) { this.goToFirstScreenByDefault() }
		}
	}
</script>
