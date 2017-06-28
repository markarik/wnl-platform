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
					:options="{ theme: 'snow', placeholder: 'Twoja wiadomość...' }"
					:toolbar="false"
					:keyboard="keyboard"
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

	export default{
		props: ['loaded', 'socket', 'room', 'inputId'],
		data() {
			return {
				disabled: false,
				error: '',
				message: '',
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
		},
		methods: {
			sendMessage(event) {
				if (this.sendingDisabled) {
					return false
				}
				this.disabled = true
				this.error = ''
				this.socket.emit('send-message', {
					room: this.room,
					message: {
						full_name: this.currentUserFullName,
						content: this.$refs.editor.quill.getText(0, this.message.length),
					}
				})
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			setListeners() {
				this.socket.on('message-processed', (data) => {
					this.disabled = false
					if (data.sent) {
						this.$refs.editor.quill.deleteText(0, this.message.length);
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
				})
			},
			onInput(input) {
				this.message = input;
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
