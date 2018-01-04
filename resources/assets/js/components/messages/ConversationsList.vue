<template lang="html">
	<div class="conversation-list">
		<wnl-conversation-snippet
			v-for="(room, index) in rooms"
			:key="index">

		</wnl-conversation-snippet>
	</div>
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

	export default {
		name: 'ConversationsList',
		components: {
			'wnl-conversation-snippet': ConversationSnippet
		},
		data() {
			return  {
				rooms: []
			}
		},
		methods: {
			getPrivateRooms() {
				axios.get(getApiUrl('chat_rooms/.getPrivateRooms'))
					.then(response => {
						this.rooms = response.data
					})
			},
			getMessages() {

			}
		},
		mounted(){
			Promise.all([
				this.getPrivateRooms(),
				this.getMessages()
			])
		}
	}
</script>