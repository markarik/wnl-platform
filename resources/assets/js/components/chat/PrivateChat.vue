<template lang="html">
	<div class="wnl-private-chat" v-if="loadRoom">
		<div class="chat-title">
			{{users[0].display_name}}
		</div>
		<wnl-private-chat-room
			:messages="room.messages",
			:users="room.users"
		/>
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
	import PrivateChatRoom from './PrivateChatRoom'

	export default {
		components: {
			'wnl-private-chat-room': PrivateChatRoom
		},
		name: 'wnl-private-chat',
		props: {
			room: {
				type: Object
			},
			users: {
				required: true,
			}
		},
		computed: {
			loadRoom() {
				return this.room !== undefined
			}
		},
		watch: {
			'room' (oldVal, newVal) {
				console.log('room changed...', this.room)
			}
		},
		mounted() {

		}
	}
</script>
