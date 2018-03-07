<template>
	<div class="wnl-chat">
		<div class="wnl-chat-messages" @scroll="onScroll"
			 ref="messagesContainer">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside" v-if="loaded">
					<div class="notification aligncenter" v-if="!hasMore">
						To początek dyskusji na tym kanale!
					</div>
					<wnl-text-loader v-if="isPulling">
						Ładuję wiadomości...
					</wnl-text-loader>
					<div v-if="messages.length > 0">
						<wnl-message v-for="(message, index) in messages"
							:key="index"
							:showAuthor="isAuthorUnique[index]"
							:id="getMessageId(message)"
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
				<wnl-text-loader v-else>Ładuję wiadomości...</wnl-text-loader>
			</div>
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
	import {getApiUrl} from 'js/utils/env'
	import {nextTick} from 'vue'
	import _ from 'lodash'
	import highlight from 'js/mixins/highlight'

	import {mapGetters, mapActions} from 'vuex'

	export default {
		components: {
			'wnl-message': Message,
		},
		props: {
			room: {
				required: true,
			},
			highlightedMessageId: {
				required: false
			},
			loaded: {
				required: true,
				type: Boolean
			},
			messages: {
				required: true,
				type: Array
			},
			pullLimit: {
				required: false,
				type: Number,
				default: 100
			},
			onScrollTop: {
				required: true,
				type: Function
			},
			hasMore: {
				required: true,
				type: Boolean
			}
		},
		data() {
			return {
				isPulling: false,
			}
		},
		mixins: [highlight],
		computed: {
			...mapGetters(['isOverlayVisible']),
			...mapGetters('chatMessages', ['getRoomById', 'getProfileByUserId']),
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30

					return message.user_id !== this.messages[previous].user_id ||
						message.time - this.messages[previous].time > halfHourInMs
				})
			},
			container() {
				return this.$el.getElementsByClassName('wnl-chat-messages')[0]
			},
			content() {
				return this.$el.getElementsByClassName('wnl-chat-content')[0]
			},
			contentInside() {
				// In case you wonder - thank Firefox :/
				return this.$el.getElementsByClassName('wnl-chat-content-inside')[0]
			},
		},
		methods: {
			scrollToBottom() {
				this.container.scrollTop = '1000000000'
			},
			scrollToTop() {
				this.container.scrollTop = '0'
			},
			pullDebouncer(event) {
				let target = event.target,
					height = target.scrollHeight,
					shouldPull =
						// make sure we're not pulling from cold storage at the moment,
						!this.isPulling &&
						// we're reaching the top of the messages container,
						(target.scrollTop / height) < 0.1 &&
						this.hasMore

				if (shouldPull) this.pull(height)
			},
			onScroll(event) {
				this.pullDebouncer.call(this, event)
			},
			pull(originalHeight) {
				this.isPulling = true
				this.onScrollTop()
					.then(() => this.isPulling = false)
			},
			scrollToMessageById(id) {
				const matchingMessage = this.$el.querySelector(`[data-id="${id}"]`)

				if (matchingMessage) {
					this.$refs.highlight = matchingMessage
					this.scrollToPositionAndHighlight(
						["chatChannel", "messageId"],
						matchingMessage.offsetTop,
						this.$refs.messagesContainer
					)
				} else {
					this.scrollToTop()
				}
			},
			getMessageId(message) {
				return `${message.time}${message.user && message.user.id}`
			},
			getMessageAuthor(message) {
				return this.getProfileByUserId(message.user_id)
			}
		},
		mounted() {
			this.pullDebouncer = _.debounce(this.pullDebouncer, 50)
		}
	}

</script>
