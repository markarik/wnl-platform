<template>
	<article class="media">
		<figure class="media-left">
			<wnl-avatar :fullName="currentUserFullName" :url="currentUserAvatar"></wnl-avatar>
		</figure>
		<div class="media-content">
			<wnl-form
				class="chat-message-form"
				:hideDefaultSubmit="true"
				name="ChatMessage"
				method="post"
				suppressEnter="false"
				resourceRoute="private_chat"
			>
				<wnl-quill
					ref="editor"
					name="text"
					:options="{ theme: 'bubble', placeholder: 'Twoja wiadomość...', formats }"
					:keyboard="keyboard"
					:toolbar="toolbar"
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
				:icon="sendingMessage ? 'refresh' : 'send-message'"
				alt="Wyślij wiadomość"
				:disabled="sendingDisabled || sendingMessage"
				:loading="sendingMessage"
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
	import { mapGetters, mapActions } from 'vuex'
	import { Quill, Form } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'
	import _ from 'lodash';
	import {SOCKET_EVENT_SEND_MESSAGE, SOCKET_EVENT_MESSAGE_PROCESSED} from 'js/plugins/socket'

	export default{
		props: {
			roomId: {
				type: Number,
				required: true
			},
			users: {
				type: Array,
				required: true
			}
		},
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
				sendingMessage: false
			}
		},
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill
		},
		computed: {
			...mapGetters([
				'currentUserFullName',
				'currentUserAvatar',
				'currentUserId',
				'currentUser'
			]),
			formats() {
				return ['bold', 'italic', 'underline', 'link']
			},
			sendingDisabled() {
				return this.message.length === 0
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
			...mapActions(['addAutoDismissableAlert']),
			sendMessage(event) {
				if (this.sendingDisabled) {
					return false
				}
				this.error = ''
				this.sendingMessage = true
				this.$socketSendMessage({
					room: this.roomId,
					message: {
						user: this.currentUser,
						content: this.content
					},
					users: this.users
				}).then(() => {
					this.quillEditor.clear();
					this.sendingMessage = false
				}).catch(err => {
					this.addAutoDismissableAlert({
						text: 'Niestety nie udało Nam się wysłać wiadomości. Spróbuj ponownie',
						type: 'error'
					})
					this.sendingMessage = false
				})
			},
			onInput(input) {
				this.message = this.quillEditor.quill.getText().trim();
				this.content = this.quillEditor.editor.innerHTML
			},
		},
		mounted () {
			this.quillEditor.quill.focus()
		},
		watch: {
			roomId() {
				this.roomId && this.quillEditor.quill.focus()
			}
		}
	}
</script>
