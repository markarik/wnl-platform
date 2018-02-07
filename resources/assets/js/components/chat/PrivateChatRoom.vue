<template>
	<div class="wnl-chat">
		<div class="wnl-chat-messages">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside">
					<div class="notification aligncenter">
						To początek dyskusji na tym kanale!
					</div>
					<div v-if="messages.length > 0">
						<wnl-message v-for="(message, index) in messages"
							 :key="index"
							 :showAuthor="isAuthorUnique[index]"
							 :id="message.id"
							 :author="getMessageAuthor(message)"
							 :fullName="getMessageAuthor(message).full_name"
							 :displayName="getMessageAuthor(message).display_name"
							 :avatar="getMessageAuthor(message).avatar"
							 :time="message.time"
							 :content="message.content"
						 ></wnl-message>
					</div>
					<div class="metadata aligncenter margin vertical" v-else>
						Napisz pierwszą wiadomość i zacznij rozmowę!
						<p class="margin vertical">
							<span class="icon is-big text-dimmed">
								<i class="fa fa-comments-o"></i>
							</span>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="wnl-chat-form">
			<!-- <wnl-message-form
				:loaded="loaded"
				:socket="socket"
				:room="room"
				ref="messageForm"
			></wnl-message-form> -->
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between

	.wnl-chat-messages
		display: flex
		flex: 1 1 0
		flex-direction: column-reverse
		overflow-y: auto

	.wnl-chat-content
		position: relative

	.wnl-chat-form
		border-top: $border-light-gray
		margin: $margin-base 0 0
		padding-top: $margin-base
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import UsersWidget from '../global/UsersWidget.vue'
	import {getApiUrl} from 'js/utils/env'
	import highlight from 'js/mixins/highlight'

	import { mapGetters } from 'vuex'

	export default {
		props: {
			messages:{
				type: Array,
				default: () => ([])
			},
			users: {
				type: Object,
				default: () => ({})
			}
		},
		computed: {
			...mapGetters(['isOverlayVisible']),
			...mapGetters('chatMessages', ['getProfileByUserId']),
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30

					return message.user_id !== this.messages[previous].user_id ||
							message.time - this.messages[previous].time > halfHourInMs
				})
			},
		},
		methods: {
			getMessageAuthor(message) {
				return this.getProfileByUserId(message.user_id)
			},
		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm,
			'wnl-users-widget': UsersWidget,
		},
	}

</script>
