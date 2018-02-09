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
					v-if="currentRoom.id"
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
				currentRoom: {},
				currentRoomUsers: []
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
			...mapGetters('chatMessages', ['getRoomById', 'getRoomProfiles', 'ready']),
			showChatRoom() {
				return !!this.currentRoom
			}
		},
		methods: {
			switchRoom({room, users}){
				this.currentRoom = room
				this.currentRoomUsers = users
			},
			roomFromRoute() {
				if (this.$route.query.roomId) {
					const roomId = this.$route.query.roomId
					this.switchRoom({room: this.getRoomById(roomId), users: this.getRoomProfiles(roomId)})
				} else if (this.$route.query.roomName) {
					// otworz pokoj o nazwie = roomName
				}
			}
		},
		watch: {
			'$route.query'() {
				this.roomFromRoute()
			}
		},
		mounted() {
			this.ready && this.roomFromRoute()
		},
		watch: {
			ready(newValue, oldValue) {
				!oldValue && newValue && this.roomFromRoute()
			}
		}
	}
</script>
