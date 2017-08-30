<template>
	<div class="quiz-question-editor">
		<p class="title is-5">Edycja pytania {{$route.params.quizId}}</p>
		<wnl-form
			class="chat-message-form"
			hideDefaultSubmit="true"
			name="QuizQuestionEditor"
			:method="formMethod"
			suppressEnter="false"
			:resourceRoute="formResourceRoute"
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

			<div class="field">
				<div class="control">
					<button class="button is-primary" @click="onFormSave">Zapisz</button>
				</div>
			</div>
		</wnl-form>

		<form ref="answersForm">
			<div
				class="field answer-field"
				v-for="(answer, index) in questionAnswers"
				:data-id="answer.id"
			>
				<h4>Odpowiedź {{index + 1}}</h4>
				<div class="control answer-control">
					<label class="label checkbox-label">
						<span>Prawidłowa?</span>
						<input
							type="checkbox"
							:name="'is_correct.' + answer.id"
							:id="answer.id"
							class="answer-correct"
							:checked="answer.is_correct"
							@change="onRightAnswerChange"
						>
					</label>
					<input
						class="input answer-text"
						:value="answer.text"
						:name="'answer.' + answer.id"
						type="text"
					>
				</div>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-question-editor
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
	
	.answer-text
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
			...mapGetters(['questionText', 'questionAnswers', 'questionId']),
			formResourceRoute() {
				if (this.method === 'post') {
					return 'quiz_questions?include=quiz_answers'
				} else {
					return `quiz_questions/${this.$route.params.quizId}?include=quiz_answers`
				}
			},
			formMethod() {
				return this.$route.params.quizId ? 'put' : 'post'
			}
		},
		methods: {
			...mapActions(['getQuizQuestion', 'saveAnswers']),
			onInput() {
				this.questionQuillContent = this.$refs.questionEditor.editor.innerHTML
			},
			onRightAnswerChange(evt) {
				const previouslyChecked = this.$el.querySelectorAll('.answer-correct')

				Array.prototype.forEach.call(previouslyChecked, checkbox => {
					if (checkbox !== evt.target) {
						checkbox.checked = false
					}
				});
			},
			onFormSave() {
				const fields = this.$refs.answersForm.querySelectorAll('.answer-field')
				const fieldsArray = Array.prototype.slice.call(fields)
				debugger
				const answersData = fieldsArray.map(
					field => ({ 
						id: field.dataset.id,
						text: field.querySelector('.answer-text').value,
						isCorrect: field.querySelector('.answer-correct').checked
					})
				)

				this.saveAnswers(answersData)
			}
		},
		watch: {
			questionText(val) {
				if (val) this.$refs.questionEditor.editor.innerHTML = val
			},
			'$route.params.quizId'(quizId) {
				this.getQuizQuestion(this.$route.params.quizId)
			}
		},
		created() {
			this.getQuizQuestion(this.$route.params.quizId);
		},
	}
</script>
