<template lang="html">
	<div class="wnl-public-chat">
		<a v-for="room, key in rooms"
		   @click="changeRoom(room)"
		   :key="key"
		   :class="{'is-active': isActive(room)}">
			{{ room.name }}
		</a>
		<wnl-chat :room="currentChannel"></wnl-chat>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-public-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
</style>

<script>
	import ChatRoom from './ChatRoom'

	export default {
		name: 'wnl-public-chat',
		props: ['rooms'],
		components: {
			'wnl-chat': ChatRoom
		},
		data () {
			return {
				currentChannel: this.rooms[0].channel,
			}
		},
		methods: {
			changeRoom(room) {
				this.currentChannel = room.channel
			},
			isActive(room){
				return room.channel === this.currentChannel
			}
		}
	}
</script>
