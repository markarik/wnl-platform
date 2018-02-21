<template lang="html">
	<div class="conversation-list">
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
		<div v-if="userSearchVisible">
			<wnl-conversations-search @close="closeUserSearch"/>
		</div>
		<div v-else-if="roomsToShow.length">
			<wnl-message-link
				v-for="(room, index) in roomsToShow"
				:key="index"
				:userId="getOtherUser(room).user_id"
				:roomId="room.id"
			>
				<wnl-conversation-snippet
					:key="index"
					:room="room"
					:profile="getOtherUser(room)"
					:isActive="isActive(room)"
				/>
        	</wnl-message-link>
		</div>
		<div v-else class="notification aligncenter">Nie masz żadnych rozmów</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

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

	.conversation-list
		width: 100%
		display: flex
		flex-direction: column
		overflow-x: hidden
		overflow-y: auto
</style>

<script>
	import axios from 'axios'
	import ConversationsSearch from 'js/components/messages/ConversationsSearch'
	import MessageLink from "js/components/global/MessageLink"
	import ConversationSnippet from "js/components/messages/ConversationSnippet"
	import {mapGetters} from 'vuex'

	export default {
		name: 'ConversationsList',
		components: {
			'wnl-conversations-search': ConversationsSearch,
			'wnl-message-link': MessageLink,
			'wnl-conversation-snippet': ConversationSnippet
		},
		data() {
			return  {
				currentRoom: '',
				userSearchVisible: false,
				searchResults: []
			}
		},
		computed: {
			...mapGetters(['currentUser']),
			...mapGetters('chatMessages', [
				'sortedRooms',
				'getProfileById',
				'getRoomById',
				'getInterlocutor'
			]),
			roomsToShow() {
				return this.sortedRooms.map(roomId => {
					return this.getRoomById(roomId)
				})
			}
		},
		methods: {
			closeUserSearch() {
				this.userSearchVisible = false
			},
			toggleUserSearch() {
				this.userSearchVisible = !this.userSearchVisible
			},
			isActive(room) {
				if (!this.userSearchVisible) {
					return this.$route.query.roomId === room.id
				}
			},
			getOtherUser(room) {
				const profile = this.getInterlocutor(room.profiles)
				if (profile.id) return profile
				return this.currentUser
			}
		}
	}
</script>
