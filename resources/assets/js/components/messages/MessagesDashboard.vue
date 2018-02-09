<template lang="html">
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside rooms-sidenav">
			    <div class="rooms-header">
					{{$t('messages.dashboard.privateMessages')}}
				</div>
				<wnl-conversations-list
					@roomSwitch="switchRoom"
				/>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<div class="scrollable-main-container chat-container">
				<wnl-private-chat
					:room="currentRoom"
					:users="currentRoomUsers"
					v-if="currentRoomUsers"
					:onSendMessage="onSendMessage"
				></wnl-private-chat>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-course-content
		flex: $course-content-flex auto
		position: relative

	.chat-container
		display: flex
		flex-direction: column

	.rooms-sidenav
		display: flex
		overflow: hidden
		flex: 1
		flex-direction: column

		.rooms-header
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			text-align: center
			padding: $margin-base
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import PrivateChat from 'js/components/chat/PrivateChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import ConversationsList from 'js/components/messages/ConversationsList'
	import * as socket from 'js/socket'

	export default {
		name: 'MessagesDashboard',
		data() {
			return {
				currentRoom: null,
				currentRoomUsers: null,
				socket: null
			}
		},
		components: {
			'wnl-main-nav': MainNav,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-private-chat': PrivateChat,
			'wnl-conversations-list': ConversationsList,
		},
		computed: {
			...mapGetters([
				'isSidenavVisible',
				'isSidenavMounted',
				'isChatMounted',
				'isChatVisible',
				'isChatToggleVisible',
				'currentUserName'
			]),
			...mapGetters('chatMessages', ['rooms', 'sortedRooms', 'getRoomById', 'getRoomProfiles', 'profiles']),
			...mapGetters('course', ['ready']),
			showChatRoom() {
				return !!this.currentRoom
			}
		},
		methods: {
			...mapActions('chatMessages', ['fetchInitialState', 'joinChannels', 'sendMessage']),
			switchRoom({room, users}){
				this.currentRoom = room
				this.currentRoomUsers = users
			},
			onSendMessage(content, callback) {
				this.sendMessage({
					event: {
						room: `channel-${this.currentRoom.id}`,
						message: {
							content,
						}
					},
					meta: {
						private: true
					},
					callback,
					socket: this.socket
				})
			}
		},
		watch: {
			'$route.query'() {
				if (this.$route.query.roomId) {
					const roomId = this.$route.query.roomId
					this.switchRoom({room: this.getRoomById(roomId), users: this.getRoomProfiles(roomId)})
				} else if (this.$route.query.roomName) {
					// otworz pokoj o nazwie = roomName
				}
			}
		},
		// beforeRouteEnter(to, from, next) {
		// 	next(vm => {
		// 		vm.fetchInitialState()
		// 			.then(() => {
		// 				// console.log(this.profiles);
        //
		// 			})
		// 	})
		// },
		mounted() {
			const roomId = this.$route.query.roomId
			roomId && this.switchRoom({room: this.getRoomById(roomId), users: this.getRoomProfiles(roomId)})
			socket.connect().then((socket) => {
				this.socket = socket
				this.joinChannels(socket)
			})
		},
		beforeDestroy() {
			socket.disconnect()
		}
	}
</script>
