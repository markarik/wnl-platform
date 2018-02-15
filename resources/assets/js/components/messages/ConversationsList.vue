<template lang="html">
	<div class="conversation-list" v-if="sortedRooms.length">
		<wnl-conversation-snippet
			v-for="roomId in sortedRooms"
			:key="roomId"
			:room="getRoomById(roomId)"
			:profiles="getRoomProfiles(roomId)"
			:messages="getRoomMessages(roomId)"
		/>
	</div>
	<div v-else class="notification aligncenter">Nie masz żadnych rozmów</div>
</template>

<style lang="sass">
	.conversation-list
		display: flex
		flex-direction: column
</style>

<script>
	import axios from 'axios'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'
	import {getApiUrl} from 'js/utils/env'
	import {mapGetters} from 'vuex'
	import {isEmpty} from 'lodash'

	export default {
		name: 'ConversationsList',
		components: {
			'wnl-conversation-snippet': ConversationSnippet
		},
		data() {
			return  {
				currentRoom: ''
			}
		},
		computed: {
			...mapGetters(['currentUserId']),
			...mapGetters('chatMessages', [
				'getRoomById',
				'sortedRooms',
				'getRoomProfiles',
				'getRoomMessages'
			]),
		},
		methods: {

		}
	}
</script>
