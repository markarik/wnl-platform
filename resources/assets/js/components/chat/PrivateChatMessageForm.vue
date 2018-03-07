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
			<span class="characters-counter metadata">{{ `${message.length} / 5000` }}</span>
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

	.characters-counter
		color: #7a7f91
		display: block
		font-weight: 400
		text-transform: none
		text-align: right

</style>
<script>
	import { mapGetters, mapActions } from 'vuex'
	import { Quill, Form } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'
	import _ from 'lodash';

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
				return this.message.length === 0 || this.message.length > 5000
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
				}).then((data) => {
					if (Object.keys(data.errors).length > 0) {
						if (data.errors.tooLong) {
							this.error = 'Nie udało się wysłać wiadomości. Wiadomość jest za duża'
						} else {
							this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
						}
					} else {
						this.quillEditor.clear();
					}
					this.sendingMessage = false
				}).catch(err => {
					$wnl.logger.capture(err)
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

				if (this.message.length > 5000) {
					this.error = "Wiadomość nie móże być dłuższa niż 5000 znaków"
				} else {
					this.error = ''
				}
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
