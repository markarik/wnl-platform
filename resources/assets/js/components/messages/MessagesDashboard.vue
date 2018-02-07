<template lang="html">
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside rooms-sidenav">
			    <div class="rooms-header">
					Prywatne wiadomości
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
					v-if="showChatRoom"
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
	import PublicChat from 'js/components/chat/PublicChat'
	import PrivateChat from 'js/components/chat/PrivateChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import ConversationsList from 'js/components/messages/ConversationsList'
	import withChat from 'js/mixins/with-chat'

	export default {
		name: 'MessagesDashboard',
		data() {
			return {
				currentRoom: null,
				currentRoomUsers: null,
			}
		},
		components: {
			'wnl-main-nav': MainNav,
			'wnl-public-chat': PublicChat,
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
			...mapGetters('chatMessages', ['rooms', 'sortedRooms', 'getRoomById', 'getRoomProfiles']),
			...mapGetters('course', ['ready']),
			showChatRoom() {
				console.log(!!this.currentRoom)
				return !!this.currentRoom
			}
		},
		methods: {
			...mapActions('chatMessages', ['fetchInitialState']),
			switchRoom({room, users}){
				this.currentRoom = room
				this.currentRoomUsers = users
			}
		},
		watch: {
			'$route.query'() {
				if (this.$route.query.roomId) {
					// otworz pokoj o id = roomid
					const roomId = this.$route.query.roomId
					this.switchRoom({room: this.getRoomById(roomId), users: this.getRoomProfiles(roomId)})
				} else if (this.$route.query.roomName) {
					// otworz pokoj o nazwie = roomName
					console.log('room name...', this.$route.query.roomName)
				}
			}
		},
		beforeRouteEnter(to, from, next) {
			next(vm => {
				return vm.fetchInitialState()
			})
		}
	}
</script>
