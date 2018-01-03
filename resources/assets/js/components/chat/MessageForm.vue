<template>
	<article class="media">
		<figure class="media-left">
			<wnl-avatar :fullName="currentUserFullName" :url="currentUserAvatar"></wnl-avatar>
		</figure>
		<div class="media-content">
			<wnl-form
				class="chat-message-form"
				hideDefaultSubmit="true"
				name="ChatMessage"
				method="post"
				suppressEnter="false"
				resourceRoute="qna_questions"
			>
				<wnl-quill
					ref="editor"
					name="text"
					:options="{ theme: 'bubble', placeholder: 'Twoja wiadomość...', formats }"
					:keyboard="keyboard"
					:toolbar="toolbar"
					:allowMentions=true
					@input="onInput"
				></wnl-quill>
			</wnl-form>
			<div class="message is-warning" v-if="error.length > 0">
				<div class="message-body">{{ error }}</div>
			</div>
		</div>
		<div class="media-right">
			<wnl-image-button
				name="wnl-chat-form-submit"
				icon="send-message"
				alt="Wyślij wiadomość"
				:disabled="sendingDisabled"
				@buttonclicked="sendMessage">
			</wnl-image-button>
		</div>
	</article>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	.media
		align-items: center
</style>
<script>
	import { mapActions, mapGetters } from 'vuex'
	import { Quill, Form } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'
	import _ from 'lodash';

	export default{
		props: ['loaded', 'socket', 'room'],
		data() {
			return {
				error: '',
				message: '',
				content: '',
				keyboard: {
					bindings: {
						tab: false,
						handleEnter: {
							key: 13,
							handler: (event) => {
								this.sendMessage(event);
								return false;
							}
						}
					}
				},
				isWaitingToSendMentions: false,
				mentions: []
			}
		},
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill
		},
		computed: {
			...mapGetters([
				'currentUserFullName',
				'currentUserDisplayName',
				'currentUserAvatar',
				'currentUserId',
				'currentUser'
			]),
			...mapGetters('course', ['courseId']),
			formats() {
				return ['bold', 'italic', 'underline', 'link', 'mention']
			},
			sendingDisabled() {
				return !this.loaded || (this.message.length === 0 && this.mentions.length === 0)
			},
			toolbar() {
				return [
					['bold', 'italic', 'underline', 'link'],
					[{ color: fontColors }],
					['clean'],
				]
			},
			quillEditor() {
				return this.$refs.editor;
			}
		},
		methods: {
			...mapActions(['saveMentions']),
			sendMessage(event) {
				if (this.sendingDisabled) {
					return false
				}
				this.error = ''
				this.isWaitingToSendMentions = true
				this.socket.emit('send-message', {
					room: this.room.channel,
					message: {
						user: this.currentUser,
						content: this.content,
					}
				})
			},
			getMentions() {
				if (!this.quillEditor) return []
				const mentions = this.quillEditor
					.$el
					.querySelectorAll('.quill-mention')

				return _.uniq(Array.prototype.map.call(mentions, el => el.dataset.id))
			},
			getMentionsData(userIds, message) {
				return {
					mentioned_users: userIds,
					subject: {
						type: 'chat_message',
						id: `${message.time}${this.currentUserId}`,
						text: message.content,
						channel: this.room.channel
					},
					objects: {
						type: "chat_channel",
						text: this.room.name
					},
					context: {
						name: this.$route.name,
						params: this.$route.params
					},
					actors: this.currentUser
				}
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			setListeners() {
				this.socket.on('message-processed', (data) => {
					if (data.sent) {
						const mentions = this.getMentions()

						if (mentions && mentions.length) {
							this.saveMentions(
								this.getMentionsData(mentions, data.message)
							)
						}

						this.mentions = []
						this.quillEditor.clear();
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
					this.isWaitingToSendMentions = false
				})
			},
			onInput(input) {
				this.message = this.quillEditor.quill.getText().trim();
				this.mentions = this.getMentions()
				this.content = this.quillEditor.editor.innerHTML
			}
		},
		watch: {
			'loaded' () {
				if (this.loaded) {
					this.setListeners()
				}
			}
		}
	}

</script>
