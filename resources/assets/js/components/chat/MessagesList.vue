<template>
	<div class="wnl-chat">
		<div class="wnl-chat-messages" @scroll="onScroll"
			 ref="messagesContainer">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside" v-if="loaded">
					<div class="notification aligncenter" v-if="!hasMore">
						To początek dyskusji na tym kanale!
					</div>
					<wnl-text-loader v-if="isPulling" class="notification aligncenter">
						Ładuję wiadomości...
					</wnl-text-loader>
					<div v-if="messages.length > 0">
						<wnl-message v-for="(message, index) in messages"
							:key="index"
							:show-author="isAuthorUnique[index]"
							:id="getMessageClientId(message)"
							:author="getMessageAuthor(message)"
							:full-name="getMessageAuthor(message).full_name"
							:display-name="getMessageAuthor(message).display_name"
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
</style>
<script>
import {SOCKET_EVENT_USER_SENT_MESSAGE} from 'js/plugins/chat-connection';
import Message from './Message.vue';
import {nextTick} from 'vue';
import _ from 'lodash';
import highlight from 'js/mixins/highlight';

import {mapGetters} from 'vuex';

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
		};
	},
	mixins: [highlight],
	computed: {
		...mapGetters(['isOverlayVisible']),
		...mapGetters('chatMessages', ['getRoomById', 'getProfileByUserId']),
		isAuthorUnique() {
			return this.messages.map((message, index) => {
				if (index === 0) return true;

				let previous     = index - 1,
					halfHourInMs = 1000 * 60 * 30;

				return message.user_id !== this.messages[previous].user_id ||
						message.time - this.messages[previous].time > halfHourInMs;
			});
		},
		container() {
			return this.$el.getElementsByClassName('wnl-chat-messages')[0];
		},
		content() {
			return this.$el.getElementsByClassName('wnl-chat-content')[0];
		},
	},
	methods: {
		scrollToBottom() {
			nextTick(() => {
				this.container.scroll({
					top: this.container.scrollHeight + 100,
					behavior: 'smooth'
				});
			});
		},
		pullDebouncer(event) {
			const target = event.target;
			const scrollPosition = target.scrollTop < 0 ?
				target.scrollHeight + target.scrollTop
				: target.scrollTop;
			const height = target.scrollHeight;
			const shouldPull =
						// make sure we're not pulling from cold storage at the moment,
						!this.isPulling &&
						// we're reaching the top of the messages container,

						(scrollPosition / height) < 0.1 &&
						this.hasMore;

			if (shouldPull) this.pull();
		},
		onScroll(event) {
			this.pullDebouncer.call(this, event);
		},
		pull() {
			this.isPulling = true;
			const heightBefore = this.container.scrollHeight;
			this.onScrollTop()
				.then(() =>  {
					const heightAfter = this.container.scrollHeight;
					this.container.scrollTop = heightAfter - heightBefore;
					this.isPulling = false;
				});
		},
		scrollToMessageById(clientId) {
			const matchingMessage = this.$el.querySelector(`[data-id="${clientId}"]`);

			if (matchingMessage) {
				this.$refs.highlight = matchingMessage;
				this.scrollToPositionAndHighlight(
					['chatChannel', 'messageId', 'messageTime', 'roomId'],
					matchingMessage.offsetTop,
					this.$refs.messagesContainer
				);
			}
		},
		getMessageClientId(message) {
			return `${message.time}${message.user_id}`;
		},
		getMessageAuthor(message) {
			return this.getProfileByUserId(message.user_id);
		}
	},
	mounted() {
		this.pullDebouncer = _.debounce(this.pullDebouncer, 300);
		this.$socketRegisterListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.scrollToBottom);
	},
	beforeDestroy() {
		this.$socketRemoveListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.scrollToBottom);
	},
	watch: {
		highlightedMessageId() {
			if (this.highlightedMessageId) this.scrollToMessageById(this.highlightedMessageId);
		},
		'loaded' (newVal) {
			// required by firefox
			this.scrollToBottom();
		}
	}
};

</script>
