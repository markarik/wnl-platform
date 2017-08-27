<template>
	<div class="quiz-editor">
		<p class="title is-5">Edycja pytania</p>
		<wnl-form
			class="chat-message-form"
			hideDefaultSubmit="true"
			name="QuizQuestionEditor"
			method="post"
			suppressEnter="false"
			resourceRoute="quiz_question"
		>
			<div class="field">
				<div class="control">
					<label class="label">
						<span>Pytanie</span>
						<wnl-quill
							ref="questionEditor"
							name="question"
							:options="{ theme: 'bubble', placeholder: 'Pytanie' }"
							:allowMentions=false
							@input="onInput"
					></wnl-quill>
					</label>
				</div>
			</div>
			<div class="field" v-for="(answer, index) in questionAnswers">
				<h4>Odpowiedź {{index + 1}}</h4>
				<div class="control answer-control">
					<label class="label checkbox-label">
						<span>Prawidłowa?</span>
						<input
							type="checkbox"
							class="right-answer-checkbox"
							:checked="answer.is_correct"
							@change="onRightAnswerChange"
						>
					</label>
					<label class="label">
						<input type="text" class="input answer-input" :value="answer.text">
					</label>
				</div>
			</div>
			<div class="field">
				<div class="control">
					<button class="button is-primary">Zapisz</button>
				</div>
			</div>
		</wnl-form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-editor
		max-width: 800px
		padding: 0 20px

	.quiz-add-question
		display: block
		margin: $margin-big 0

	.quill-container
		height: 200px

	.answer-control
		display: flex
		padding: 10px 0

		.label
			align-items: center
			display: flex
			flex-grow: 1
	
		.checkbox-label
			flex-grow: 0
			margin-right: 20px

			span
				margin-right: 8px
	
	.answer-input
		flex-grow: 1
</style>

<script>
	import { mapActions, mapGetters } from 'vuex';
	import { Quill, Form } from 'js/components/global/form'
	import {nextTick} from 'vue'

	export default {
		name: 'QuizesEditor',
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill
		},
		data: function () {
			return {
				questionQuillContent: ''
			}
		},
		computed: {
			...mapGetters(['questionText', 'questionAnswers'])
		},
		methods: {
			...mapActions(['getQuizQuestion']),
			onInput() {
				this.questionQuillContent = this.$refs.questionEditor.editor.innerHTML
			},
			onRightAnswerChange(evt) {
				const previouslyChecked = this.$el.querySelectorAll('.right-answer-checkbox')

				Array.prototype.forEach.call(previouslyChecked, checkbox => {
					if (checkbox !== evt.target) {
						checkbox.checked = false
					}
				});
			}
		},
		watch: {
			questionText(val) {
				if (val) this.$refs.questionEditor.editor.innerHTML = val
			},
			'$route.params.quizId'(quizId) {
				this.getQuizQuestion(this.$route.params.quizId);
			}
		},
		created() {
			this.getQuizQuestion(this.$route.params.quizId);
		},
	}
</script>
