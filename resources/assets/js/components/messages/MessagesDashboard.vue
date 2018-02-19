<template lang="html">
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside rooms-sidenav">
			    <div class="rooms-header">
					<header>{{$t('messages.dashboard.privateMessages')}}</header>
					<div class="rooms-list-controls">
					<span class="rooms-list-controls-item is-active" @click="toggleUserSearch">
						<div class="search-icon" v-if="!userSearchVisible">
							<i class="fa fa-search" :title="$t('messages.search.searchButton')"></i>
							<span class="text">{{$t('messages.search.searchButton')}}</span>
						</div>
						<div class="close-icon" v-else>
							<i class="fa fa-times" :title="$t('messages.search.closeButton')"></i>
							<span class="text">{{$t('messages.search.closeButton')}}</span>
						</div>
					</span>
					</div>
				</div>
				<wnl-find-users
					v-if="userSearchVisible"
					@close="toggleUserSearch"
				/>
				<wnl-conversations-list v-else/>
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

		.rooms-header
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			display: flex
			justify-content: space-between
			border-bottom: $border-light-gray
			overflow-y: hidden
			min-height: $margin-huge

			header
				margin: $margin-base

			.rooms-list-controls-item
				padding: $margin-small
				display: flex
				align-items: center
				height: 100%

				.text
					text-transform: uppercase
					font-size: $font-size-minus-3

				&:hover, &.is-active
					background: $color-background-lighter-gray
					cursor: pointer

				.search-icon
					display: flex
					flex-direction: column

				.close-icon
					display: flex
					flex-direction: column


</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import MainNav from 'js/components/MainNav'
	import PrivateChat from 'js/components/chat/PrivateChat'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import ConversationsList from 'js/components/messages/ConversationsList'
	import FindUsers from 'js/components/messages/FindUsers'
	import * as socket from 'js/socket'

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
			}
		},
		methods: {
			switchRoom({room, users}){
				this.currentRoom = room
				this.currentRoomUsers = users
			},
			roomFromRoute() {
				const roomId = this.$route.query.roomId
				if (roomId) {
					const room = this.getRoomById(roomId)
					if (room.id) {
						this.switchRoom({room, users: this.getRoomProfiles(roomId)})
					} else {
						const {roomId, ...query} = this.$route.query
						this.$router.replace({
							...this.$route,
							query
						})
					}
				}
			},
			toggleUserSearch(){
				this.userSearchVisible = !this.userSearchVisible
			}
		},
		watch: {
			'$route.query'() {
				this.roomFromRoute()
			},
			ready(newValue, oldValue) {
				!oldValue && newValue && this.roomFromRoute()
			}
		},
		mounted() {
			this.ready && this.roomFromRoute()
		}
	}
</script>
