<template lang="html">
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
			:isMaxWidth="true"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside rooms-sidenav">
				<wnl-conversations-list/>
			</aside>
		</wnl-sidenav-slot>
		<div class="scrollable-main-container chat-container">
			<wnl-private-chat
				:room="currentRoom"
				:users="currentRoomUsers"
				v-if="currentRoom.id"
			></wnl-private-chat>
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
		width: 100%


</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import PrivateChat from 'js/components/chat/PrivateChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import ConversationsList from 'js/components/messages/ConversationsList'
	import FindUsers from 'js/components/messages/FindUsers'

	export default {
		name: 'MessagesDashboard',
		data() {
			return {
				currentRoom: {},
				currentRoomUsers: [],
				userSearchVisible: false,
			}
		},
		components: {
			'wnl-main-nav': MainNav,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-private-chat': PrivateChat,
			'wnl-conversations-list': ConversationsList,
			'wnl-find-users': FindUsers,
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
			...mapGetters('chatMessages', ['getRoomById', 'getRoomProfiles', 'ready', 'rooms', 'sortedRooms', 'profiles']),
			showChatRoom() {
				return !!this.currentRoom
			},
			mostRecentRoomId() {
				return this.sortedRooms[0]
			}
		},
		methods: {
			...mapActions('chatMessages', ['markRoomAsRead']),
			switchRoom({room, users}){
				this.currentRoom = room
				this.currentRoomUsers = users
				room.id && this.$socketMarkRoomAsRead(room.id)
					.then(() => this.markRoomAsRead(room.id))
					.catch(err => $wnl.logger.capture(err))
			},
			openRoomById(roomId) {
				const room = this.getRoomById(roomId)
				if (room.id) {
					this.switchRoom({room, users: this.getRoomProfiles(roomId)})
					return true
				} else {
					const {roomId, ...query} = this.$route.query
					this.$router.replace({
						...this.$route,
						query
					})
					return false
				}
			},
			openInitialRoom() {
				const roomId = this.$route.query.roomId
				const roomExists = this.openRoomById(roomId)

				if (!roomExists) {
					this.$router.replace({
						...this.$route,
						query: {
							...this.$route.query,
							roomId: this.mostRecentRoomId
						}
					})
				}
			},
		},
		watch: {
			'$route.query.roomId'(roomId) {
				roomId && this.openRoomById(roomId)
			},
			ready(newValue, oldValue) {
				!oldValue && newValue && this.openInitialRoom()
			}
		},
		mounted() {
			this.ready && this.openInitialRoom()
		}
	}
</script>
