<template lang="html">
	<div class="wnl-private-chat">
		<div class="chat-title">
			{{chatTitle}}
		</div>
		<div class="wnl-chat">
			<div class="wnl-chat-messages" @scroll="onScroll">
				<div class="wnl-chat-content">
					<div class="wnl-chat-content-inside" v-if="true">
						<!-- <div class="notification aligncenter" v-if="!thereIsMore">
							To początek dyskusji na tym kanale!
						</div> -->
						<!-- <wnl-text-loader v-if="isPulling">
							Ładuję wiadomości...
						</wnl-text-loader> -->
						<div v-if="room.messages.length">
							<wnl-message v-for="(message, index) in room.messages"
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
					<wnl-text-loader v-else>
						Ładuję wiadomości...
					</wnl-text-loader>
				</div>
			</div>
			<div class="wnl-chat-form">
				<wnl-private-chat-message-form
					:roomId="room.id"
					:users="users"
					ref="messageForm"
				></wnl-private-chat-message-form>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-private-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		position: relative
		width: 100%

	.chat-title
		text-align: center

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
	import PrivateChatMessageForm from './PrivateChatMessageForm.vue'
	import UsersWidget from '../global/UsersWidget.vue'
	import {getApiUrl} from 'js/utils/env'
	import highlight from 'js/mixins/highlight'

	import { mapGetters } from 'vuex'

	export default {
		components: {
			'wnl-message': Message,
			'wnl-private-chat-message-form': PrivateChatMessageForm,
			'wnl-users-widget': UsersWidget,
		},
		props: {
			room: {
				type: Object,
				required: true
			},
			users: {
				type: Array,
				required: true
			}
		},
		computed: {
			...mapGetters(['isOverlayVisible', 'currentUserId', 'currentUserDisplayName']),
			...mapGetters('chatMessages', ['getProfileByUserId', 'profiles', 'getInterlocutor']),
			isAuthorUnique() {
				return this.room.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30

					return message.user_id !== this.room.messages[previous].user_id ||
							message.time - this.room.messages[previous].time > halfHourInMs
				})
			},
			chatTitle() {
				const matchingProfile = this.getInterlocutor(this.room.profiles)
				return matchingProfile.display_name || this.currentUserDisplayName
			}
		},
		methods: {
			onScroll(event) {
				console.log(event);
				// this.pullDebouncer.call(this, sevent)
			},
			pullDebouncer(event) {
				let target     = event.target,
					height     = target.scrollHeight,
					shouldPull =
							// We're always getting first 200 messages from hot storage,
							this.messages.length >= 200 &&
							// make sure we're not pulling from cold storage at the moment,
							!this.isPulling &&
							// we're reaching the top of the messages container,
							(target.scrollTop / height) < 0.1 &&
							// cold storage has some more messages we can pull.
							this.thereIsMore

				if (shouldPull) this.pull(height)
			},
			getMessageAuthor(message) {
				return this.getProfileByUserId(message.user_id)
			}
		},
	}

</script>
