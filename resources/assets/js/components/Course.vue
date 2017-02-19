<template>
	<div class="columns">
		<div class="column">
			<wnl-sidenav :api-url="navigationApiUrl"></wnl-sidenav>
		</div>
		<div class="column is-half">

		</div>
		<div class="column">
			<wnl-chat :room="1"></wnl-chat>
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
