<template lang="html">
	<div class="wnl-private-chat">
		<div class="chat-title">
			Adam Karmiński
		</div>
		<wnl-chat :room="room"></wnl-chat>
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
			users: {
				required: true,
				type: Array,
				default: () => [],
			},
			title: {
				required: false,
				type: String,
				default: '',
			}
		},
		components: {
			'wnl-chat': ChatRoom
		},
		computed: {
			room() {
				return {channel: 'private-1-2'}
			}
		}
	}
</script>
