<template lang="html">
	<div class="wnl-public-chat">
		<div class="chat-title">
			{{title || chatTitle}}
		</div>
		<div class="tabs">
			<ul>
				<li  v-for="room, key in rooms" :key="key" :class="{'is-active': isActive(room)}">
					<a @click="changeRoom(room)">{{ room.name }}</a>
				</li>
			</ul>
		</div>
		<a class="wnl-chat-close">
			<span v-if="canShowCloseIconInChat" class="icon wnl-chat-close" @click="toggleChat">
				<i class="fa fa-chevron-right"></i>
				<span>Ukryj czat</span>
			</span>
		</a>
		<wnl-chat :room="currentRoom"></wnl-chat>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-public-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		padding: $margin-base
		position: relative

		.wnl-chat-close
			color: $color-ocean-blue
			cursor: pointer
			display: flex
			flex-direction: column
			position: absolute
			right: $margin-base
			top: $margin-base

			span
				font-size: $font-size-minus-4
				text-transform: uppercase
				white-space: nowrap


	.metadata
		margin: $margin-base 0 0 $margin-base

	.chat-title
		color: $color-gray-dimmed
		font-size: $font-size-minus-2
		text-transform: uppercase

</style>

<script>
	import ChatRoom from './ChatRoom'
	import { mapActions, mapGetters } from 'vuex'
	import _ from 'lodash'

	export default {
		name: 'wnl-public-chat',
		components: {
			'wnl-chat': ChatRoom
		},
		props: ['title', 'rooms'],
		data () {
			return {
				currentRoom: this.getCurrentRoom()
			}
		},
		computed: {
			...mapGetters(['canShowCloseIconInChat']),
			...mapGetters('course', ['getLesson']),
			chatTitle() {
				let lessonId = this.$route.params.lessonId

				if(typeof lessonId === 'undefined') {
					return 'Ogólny czat kursu'
				}

				return `Czat lekcji ${this.getLesson(lessonId).name}`
			}
		},
		methods: {
			...mapActions(['toggleChat']),
			changeRoom(room) {
				this.currentRoom = room
			},
			isActive(room){
				return room.channel === this.currentChannel
			},
			getCurrentRoom() {
				const query = this.$route.query

				if (query.chatChannel) {
					const room = _.find(this.rooms, room => room.channel === query.chatChannel)

					if (room) {
						return room
					} else {
						this.cleanupChatChannelParam()
						return this.rooms[0]
					}
				} else {
					return this.rooms[0]
				}
			},
			cleanupChatChannelParam() {
				const query = this.$route.query

				delete query.chatChannel

				this.$router.replace({
					...this.$route,
					query
				})
			}
		},
		watch: {
			'rooms' (newValue, oldValue) {
				if (newValue.length === oldValue.length) return
				this.changeRoom(newValue[0])
			},
			'$route.query.chatChannel' () {
				this.changeRoom(this.getCurrentRoom());
			}
		}
	}
</script>
