<template>
	<div class="columns">
		<div class="column">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="column is-half">
			<wnl-chat :roomId="chatRoomId"></wnl-chat>
		</div>
		<div class="column">

		</div>
	</div>
</template>

<script>
	import Sidenav from './Sidenav.vue'
	import Chat from './chat/Chat.vue'

	export default {
		name: 'Course',
		props: ['courseId'],
		computed: {
			chatRoomId() {
				return 'courses-' + this.courseId
			},
			navigationApiUrl() {
				return $fn.getApiUrl('courses/' + this.courseId + '/nav')
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-chat': Chat
		}
	}
</script>
