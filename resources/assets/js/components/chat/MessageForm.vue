<template>
	<div class="wnl-chat-form">
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
				<span class="characters-counter metadata">{{ `${message.length} / 5000` }}</span>
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
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.media
		align-items: center

	.characters-counter
		color: #7a7f91
		display: block
		font-weight: 400
		text-transform: none
		text-align: right

	.wnl-chat-form
		border-top: $border-light-gray
		margin: $margin-base 0 0
		padding-top: $margin-base

</style>
<script>
	import { mapActions, mapGetters } from 'vuex'
	import { Quill, Form } from 'js/components/global/form'
	import { fontColors } from 'js/utils/colors'
	import _ from 'lodash';

	export default{
		props: {
			room: {
				type: Object,
				required: true
			},
			loaded: {
				type: Boolean,
				default: true
			},
			messagePayload: {
				type: Object,
				default: () => ({})
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
				'currentUserAvatar',
				'currentUser'
			]),
			formats() {
				return ['bold', 'italic', 'underline', 'link', 'mention']
			},
			sendingDisabled() {
				return !this.loaded || (this.message.length === 0 && this.mentions.length === 0) || this.message.length > 5000
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
				this.isWaitingToSendMentions = true
				this.$socketSendMessage({
					room: this.room.id,
					message: {
						user: this.currentUser,
						content: this.content
					},
					...this.messagePayload
				}).then(data => {
					this.processMessage(data)
					this.$emit('messageSent', data)
				}).catch((err) => {
					$wnl.logger.capture(err)
					this.addAutoDismissableAlert({
						text: 'Niestety nie udało Nam się wysłać wiadomości. Spróbuj ponownie',
						type: 'error'
					})
				})
			},
			getMentions() {
				if (!this.quillEditor) return []
				const mentions = this.quillEditor
					.$el
					.querySelectorAll('.quill-mention')

				return _.uniq(Array.prototype.map.call(mentions, el => el.dataset.id))
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			processMessage(data) {
				if (data.sent) {
					const mentions = this.getMentions()

					if (mentions && mentions.length) {
						this.$emit('foundMentions', {mentions, context: data.message})
					}

					this.mentions = []
					this.quillEditor.clear();
				} else {
					if (data.errors && data.errors.tooLong) {
						this.error = 'Nie udało się wysłać wiadomości. Wiadomość jest za duża'
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
				}
			},
			onInput(input) {
				this.message = this.quillEditor.quill.getText().trim();
				this.mentions = this.getMentions()
				this.content = this.quillEditor.editor.innerHTML
				if (this.message.length > 5000) {
					this.error = "Wiadomość nie móże być dłuższa niż 5000 znaków"
				} else {
					this.error = ''
				}
			}
		}
	}

</script>
