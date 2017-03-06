<template>
	<div class="wnl-app-layout">
		<div class="wnl-app-layout-left">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="wnl-app-layout-main">

		</div>
		<div class="wnl-app-layout-right">
			<wnl-chat :room="chatRoom"></wnl-chat>
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
			chatRoom() {
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
