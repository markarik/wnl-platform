<template>
	<div class="wnl-chat-form">
		<article class="media">
			<figure class="media-left">
				<wnl-avatar :full-name="currentUserFullName" :url="currentUserAvatar" />
			</figure>
			<div class="media-content wnl-chat-form-wrapper">
				<wnl-form
					:id="formId"
					class="chat-message-form"
					hide-default-submit="true"
					name="ChatMessage"
					method="post"
					suppress-enter="false"
					resource-route="qna_questions"
				>
					<wnl-quill
						id="elo-elo"
						ref="editor"
						name="text"
						:options="quillOptions"
						:keyboard="keyboard"
						:toolbar="toolbar"
						:allow-mentions="true"
						@input="onInput"
					/>
				</wnl-form>
				<span class="characters-counter metadata">{{`${message.length} / 5000`}}</span>
				<div v-if="error.length > 0" class="message is-warning">
					<div class="message-body">{{error}}</div>
				</div>
			</div>
			<div class="media-right">
				<wnl-image-button
					name="wnl-chat-form-submit"
					:icon="sendingMessage ? 'refresh' : 'send-message'"
					alt="Wyślij wiadomość"
					:disabled="sendingDisabled || sendingMessage"
					:loading="sendingMessage"
					@buttonclicked="sendMessage"
				/>
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

	.wnl-chat-form-wrapper
		position: relative

</style>
<script>
import { mapActions, mapGetters } from 'vuex';
import { Quill, Form } from 'js/components/global/form';
import { fontColors } from 'js/utils/colors';
import { nextTick } from 'vue';
import _ from 'lodash';

export default{
	components: {
		'wnl-form': Form,
		'wnl-quill': Quill
	},
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
		},
		autofocusOnRoomChange: {
			type: Boolean,
			default: false
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
						handler: () => {
							this.sendMessage();
							return false;
						}
					}
				}
			},
			isWaitingToSendMentions: false,
			mentions: [],
			sendingMessage: false
		};
	},
	computed: {
		...mapGetters([
			'currentUserFullName',
			'currentUserAvatar',
		]),
		sendingDisabled() {
			return !this.loaded || (this.message.length === 0 && this.mentions.length === 0) || this.message.length > 5000;
		},
		toolbar() {
			return [
				['bold', 'italic', 'underline', 'link'],
				[{ color: fontColors }],
				['clean'],
			];
		},
		quillOptions() {
			return {
				theme: 'bubble',
				placeholder: 'Twoja wiadomość...',
				formats: ['bold', 'italic', 'underline', 'link', 'mention'],
				bounds: `#${this.formId}`,
			};
		},
		quillEditor() {
			return this.$refs.editor;
		},
		formId() {
			return `chat-message-form-${this._uid}`;
		}
	},
	watch: {
		'room.id'() {
			if (this.autofocusOnRoomChange && this.room.id) {
				nextTick(() => this.quillEditor.quill.focus());
			}
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		sendMessage() {
			if (this.sendingDisabled) {
				return false;
			}
			this.sendingMessage = true;
			this.error = '';
			this.isWaitingToSendMentions = true;
			const { messages, ...room } = this.room;
			this.$socketSendMessage({
				room,
				message: {
					content: this.content
				},
				...this.messagePayload
			}).then(data => {
				this.processMessage(data);
				this.$emit('messageSent', data);
				this.sendingMessage = false;
			}).catch((err) => {
				$wnl.logger.error(err);
				this.addAutoDismissableAlert({
					text: 'Niestety nie udało Nam się wysłać wiadomości. Spróbuj ponownie',
					type: 'error'
				});
				this.sendingMessage = false;
			});
		},
		getMentions() {
			if (!this.quillEditor) return [];
			const mentions = this.quillEditor
				.$el
				.querySelectorAll('.quill-mention');

			return _.uniq(Array.prototype.map.call(mentions, el => el.dataset.id));
		},
		suppressEnter(event) {
			event.preventDefault();
		},
		processMessage(data) {
			if (data.sent) {
				const mentions = this.getMentions();

				if (mentions && mentions.length) {
					this.$emit('foundMentions', { mentions, context: data.message });
				}

				this.mentions = [];
				this.quillEditor.clear();
			} else {
				if (data.errors && data.errors.tooLong) {
					this.error = 'Nie udało się wysłać wiadomości. Wiadomość jest za duża';
				} else {
					this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)';
				}
			}
		},
		onInput() {
			this.message = this.quillEditor.quill.getText().trim();
			this.mentions = this.getMentions();
			this.content = this.quillEditor.editor.innerHTML;
			if (this.message.length > 5000) {
				this.error = 'Wiadomość nie móże być dłuższa niż 5000 znaków';
			} else {
				this.error = '';
			}
		}
	},
};

</script>
