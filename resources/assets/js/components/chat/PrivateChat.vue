<template lang="html">
	<div class="wnl-private-chat" v-if="loadRoom">
		<div class="chat-title">
			{{users[0].display_name}}
		</div>
		<wnl-chat
			:room="room"
			:initialPull="initialPull"
			:pullLimit="pullLimit"
		>
		</wnl-chat>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-private-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		position: relative
		width: 100%

	.chat-title
		text-align: center

</style>

<script>
	import ChatRoom from './ChatRoom'

	export default {
		name: 'wnl-private-chat',
		props: {
			room: {
				required: true,
			},
			title: {
				required: false,
				type: String,
				default: '',
			},
			users: {
				required: true,
			}
		},
		data() {
			return {
				initialPull: true,
				pullLimit: 30,
			}
		},
		components: {
			'wnl-chat': ChatRoom
		},
		computed: {
			loadRoom() {
				return this.room !== undefined
			}
		},
		watch: {
			'room' (oldVal, newVal) {

			}
		},
		mounted() {

		}
	}
</script>
