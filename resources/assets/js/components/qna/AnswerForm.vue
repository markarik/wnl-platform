<template>
	<article class="media">
		<figure class="media-left">
			<wnl-avatar :username="currentUserFullName"></wnl-avatar>
		</figure>
		<div class="media-content">
			<p class="control">
				<textarea v-model="message" class="textarea wnl-qna-textarea"
						  :style="{ height: textareaHeight }"
						  :disabled="disabled"
						  @keydown.enter="suppressEnter"
						  @keyup.enter="postEntry">
				</textarea>
			</p>
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
					@buttonclicked="postEntry">
			</wnl-image-button>
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-form-textarea
		min-height: map-get($rounded-square-sizes, 'medium')
		overflow: hidden
		padding: 6px 10px
		resize: none
</style>

<script>
	import {mapGetters, mapActions} from 'vuex'
	import {getApiUrl} from 'js/utils/env'

	export default{
		name: 'AnswerForm',
		props: ['lessonId', 'questionId'],
		data(){
			return {
				disabled: false,
				error: '',
				message: '',
				canvasContext: null,
				textarea: {},
				computedStyles: {}
			}
		},
		computed: {
			...mapGetters([
				'currentUserFullName'
			]),
			sendingDisabled() {
				return this.message.length === 0
			},
			textareaHeight() {
				if (this.canvasContext !== null && this.message.length > 0) {
					return this.calculateTextareaHeight(this.message)
				} else {
					return null
				}
			}
		},
		methods: {
//			setTextareaStyles() {
//				this.textarea = document.getElementById(this.inputId)
//				this.computedStyles = window.getComputedStyle(this.textarea)
//			},
			...mapActions(['qnaAddAnswer']),
			setTextMeasureCanvas() {
				let canvas, context

				canvas             = this.measureCanvas || document.createElement('canvas')
				context            = canvas.getContext('2d')
				context.font       = `${this.computedStyles.fontSize} ${this.computedStyles.fontFamily}`
				this.canvasContext = context

				return true
			},
			calculateTextareaHeight(message) {
				// TODO: Mar 5, 2017 - Use the same padding declaration for styling
				const padding = 6
				let lines     = Math.max(
						Math.ceil(
								this.canvasContext.measureText(message).width * 1.2 / parseInt(this.computedStyles.width)
						),
						1)
				if (lines > 1) {
					return `${padding * 2 + lines * parseInt(this.computedStyles.lineHeight)}px`
				}
				return null
			},
			postEntry(event) {
				if (this.sendingDisabled) {
					return false
				}
//				this.disabled = true
				this.error = ''
				console.log(this.lessonId)
				this.qnaAddAnswer({
					lessonId: this.lessonId,
					questionId: this.questionId,
					text: this.message
				}).then(() => {
					this.message  = ''
					this.disabled = false
				}).catch(() => {
					this.error    = 'Coś poszło nie tak... Proszę, spróbuj jeszcze raz. :)'
					this.disabled = false
				})
			},
			suppressEnter(event) {
				event.preventDefault()
			},
		},
	}

</script>
