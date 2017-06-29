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
					class="margin bottom"
					name="text"
					:options="{ theme: 'bubble', placeholder: 'Twoja wiadomość...' }"
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
				icon="send-message"
				alt="Wyślij wiadomość"
				:disabled="sendingDisabled"
				@buttonclicked="sendMessage">
			</wnl-image-button>
		</div>
	</article>
</template>

<script>
	import { mapGetters } from 'vuex'
	import { Quill, Form } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'

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
				}
			}
		},
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill
		},
		computed: {
			...mapGetters([
				'currentUserFullName',
				'currentUserAvatar'
			]),
			sendingDisabled() {
				return !this.loaded || this.message.length === 0
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
			sendMessage(event) {
				if (this.sendingDisabled) {
					return false
				}
				this.error = ''
				this.socket.emit('send-message', {
					room: this.room,
					message: {
						full_name: this.currentUserFullName,
						content: this.content
					}
				})
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			setListeners() {
				this.socket.on('message-processed', (data) => {
					if (data.sent) {
						this.quillEditor.quill.deleteText(0, this.content.length);
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
				})
			},
			onInput(input) {
				this.message = this.quillEditor.quill.getText().trim();
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
