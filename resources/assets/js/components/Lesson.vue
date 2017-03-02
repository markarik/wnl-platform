<template>
	<div class="columns">
		<div class="column">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="column is-half">
			<router-view></router-view>
		</div>
		<div class="column">
			<wnl-chat :room="chatRoom"></wnl-chat>
		</div>
	</div>
</template>

<script>
	import Sidenav from './Sidenav.vue'
	import Chat from './chat/Chat.vue'
	import { mapGetters } from 'vuex'

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId', 'screenId', 'slide'],
		computed: {
			chatRoom() {
				return 'courses-' + this.courseId + '-lessons-' + this.lessonId
			},
			navigationApiUrl() {
				return $fn.getApiUrl('lessons/' + this.lessonId + '/nav')
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
