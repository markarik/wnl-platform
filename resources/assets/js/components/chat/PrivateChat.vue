<template lang="html">
	<div class="wnl-private-chat" v-if="loadRoom">
		<div class="chat-title">
			{{room.channel}}
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
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
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
				console.log(newVal)
			}
		},
		mounted() {
			console.log(this.room)
		}
	}
</script>
