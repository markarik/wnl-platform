<template lang="html">

	<div>
		<wnl-find-users
			@updateItems="onUpdateItems"
			@onKeyDown="onKeyDown"
		/>
		<wnl-message-link
				v-for="(room, index) in roomsToShow"
				:key="index"
				:userId="getOtherUser(room).user_id"
				:roomId="room.id"
				@click="onClose"
				ref="messageLink"
			>
			<wnl-conversation-snippet
				:key="index"
				:room="room"
				:isActive="index === activeIndex"
				:profile="getOtherUser(room)"
			/>
		</wnl-message-link>
	</div>

</template>

<script>
	import autocomplete from 'js/mixins/autocomplete-nav'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'
	import FindUsers from 'js/components/messages/FindUsers'
	import MessageLink from "js/components/global/MessageLink"
	import {mapGetters} from 'vuex'

	export default {
		name: 'UsersAutocomplete',
		components: {
			'wnl-conversation-snippet': ConversationSnippet,
			'wnl-find-users': FindUsers,
			'wnl-message-link': MessageLink
		},
		mixins: [autocomplete],
		data() {
			return {
				items: []
			}
		},
		computed: {
			...mapGetters(['currentUser']),
			...mapGetters('chatMessages', ['getRoomForPrivateChat']),
			roomsToShow() {
				return this.items.map(profile => {
					const room = this.getRoomForPrivateChat(profile.user_id)

					return {
						messages: [],
						...room,
						profiles: [this.currentUser, profile]
					}
				})
			}
		},
		methods: {
			onClose() {
				this.$emit('close')
			},
			onItemChosen(item, itemIndex) {
				this.$refs.messageLink[itemIndex].navigate()
					.then(this.onClose)
			},
			onUpdateItems(items) {
				this.items = items
			},
			getOtherUser(room) {
				const profile = room.profiles.find(profile => profile.user_id !== this.currentUser.user_id)
				if (profile) return profile
				return this.currentUser
			}
		}
	}
</script>
