<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-max-width="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside rooms-sidenav">
				<wnl-conversations-list/>
			</aside>
		</wnl-sidenav-slot>
		<div class="scrollable-main-container chat-container">
			<wnl-private-chat
				:room="currentRoom"
				:users="currentRoomUsers"
				:messages-loaded="messagesLoaded"
				v-if="currentRoom.id"
			></wnl-private-chat>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout
		width: 100%

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
import { mapActions, mapGetters } from 'vuex';

import MainNav from 'js/components/MainNav';
import PrivateChat from 'js/components/chat/PrivateChat';
import SidenavSlot from 'js/components/global/SidenavSlot';
import ConversationsList from 'js/components/messages/ConversationsList';
import { first } from 'lodash';

export default {
	name: 'MessagesDashboard',
	data() {
		return {
			currentRoom: {},
			currentRoomUsers: [],
			messagesLoaded: true,
		};
	},
	components: {
		'wnl-main-nav': MainNav,
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
		...mapGetters('chatMessages', ['getRoomById', 'getRoomProfiles', 'ready', 'rooms', 'sortedRooms', 'profiles']),
		showChatRoom() {
			return !!this.currentRoom;
		},
		mostRecentRoomId() {
			return this.sortedRooms[0];
		}
	},
	methods: {
		...mapActions(['toggleOverlay']),
		...mapActions('chatMessages', ['markRoomAsRead', 'fetchRoomMessages']),
		switchRoom({ room, users }){
			this.currentRoom = room;
			this.currentRoomUsers = users;
			const { messages, ...roomNoMessages } = room;
			room.id && this.$socketMarkRoomAsRead(roomNoMessages)
				.then(() => this.markRoomAsRead(room.id))
				.catch(err => $wnl.logger.capture(err));

			const context = {
				roomId: room.id
			};

			if (room.pagination && room.pagination.next) {
				context.messageTime =  room.pagination.next;
				context.afterLimit = 0;
				context.beforeLimit = PrivateChat.PRIVATE_CHAT_MESSAGES_LIMIT;
			} else if (room.messages && room.messages.length) {
				context.messageTime = first(room.messages).time;
				context.afterLimit = 0;
				context.beforeLimit = PrivateChat.PRIVATE_CHAT_MESSAGES_LIMIT;
			}

			this.messagesLoaded = false;
			this.fetchRoomMessages({
				room,
				limit: PrivateChat.PRIVATE_CHAT_MESSAGES_LIMIT,
				context,
				append: true
			}).then(() => this.messagesLoaded = true);
		},
		openRoomById(roomId) {
			const room = this.getRoomById(roomId);
			if (room.id) {
				this.switchRoom({ room, users: this.getRoomProfiles(roomId) });
				return true;
			} else {
				const { roomId, ...query } = this.$route.query;
				this.$router.replace({
					...this.$route,
					query
				});
				return false;
			}
		},
		openInitialRoom() {
			const roomId = this.$route.query.roomId;
			const roomExists = this.openRoomById(roomId);

			if (!roomExists) {
				this.$router.replace({
					...this.$route,
					query: {
						...this.$route.query,
						roomId: this.mostRecentRoomId
					}
				});
			}
		},
	},
	watch: {
		'$route.query.roomId'(roomId) {
			roomId && this.openRoomById(roomId);
		},
		ready(newValue, oldValue) {
			!oldValue && newValue && this.openInitialRoom();
			newValue && this.toggleOverlay({ source: 'messagesDashboard', display: false });
		}
	},
	mounted() {
		!this.ready && this.toggleOverlay({ source: 'messagesDashboard', display: true });
		this.ready && this.openInitialRoom();
	}
};
</script>
