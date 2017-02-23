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

	export default {
		name: 'Lesson',
		props: ['courseId', 'lessonId'],
		computed: {
			chatRoom() {
				return 'courses-' + this.courseId + '-lessons-' + this.lessonId
			},
			navigationApiUrl() {
				return $fn.getApiUrl('lessons/' + this.lessonId + '/nav')
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-chat': Chat
		}
	}
</script>
