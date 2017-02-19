<template>
	<div class="columns is-gapless">
		<div class="column wnl-background-gray">
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
		props: {
			id: {
				type: Number,
				default: 1
			}
		},
		computed: {
			navigationApiUrl() {
				return $fn.getApiUrl('courses/' + this.id + '/nav')
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-chat': Chat
		}
	}
</script>
