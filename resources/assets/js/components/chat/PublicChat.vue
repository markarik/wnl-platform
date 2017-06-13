<template lang="html">
	<div class="wnl-public-chat">
		<a v-for="room, key in rooms"
		   @click="changeRoom(room)"
		   :key="key"
		   :class="{'is-active': isActive(room)}">
			{{ room.name }}
		</a>
		<span v-if="canShowCloseIconInChat" class="icon wnl-chat-close" @click="toggleChat">
			<i class="fa fa-close"></i>
		</span>
		<wnl-chat :room="currentChannel"></wnl-chat>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	.wnl-public-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		position: relative

		.wnl-chat-close
			color: $color-ocean-blue
			cursor: pointer
			position: absolute
			right: 1em
			top: 1em
</style>

<script>
	import ChatRoom from './ChatRoom'
	import { mapActions, mapGetters } from 'vuex'

	export default {
		name: 'wnl-public-chat',
		props: ['rooms'],
		components: {
			'wnl-chat': ChatRoom
		},
		computed: {
			...mapGetters(['canShowCloseIconInChat'])
		},
		data () {
			return {
				currentChannel: this.rooms[0].channel,
			}
		},
		methods: {
			...mapActions(['toggleChat']),
			changeRoom(room) {
				this.currentChannel = room.channel
			},
			isActive(room){
				return room.channel === this.currentChannel
			}
		}
	}
</script>
