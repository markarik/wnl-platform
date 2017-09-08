<template>
	<div class="quiz-question-editor">
		<wnl-form
			:attach="attach"
			class="chat-message-form"
			hideDefaultSubmit="true"
			name="QuizQuestionEditor"
			:method="formMethod"
			suppressEnter="false"
			:resourceRoute="formResourceRoute"
			@submitSuccess="onSubmitSuccess"
		>
			<div class="question-form-header">
				<p class="title is-5">Edycja pytania {{$route.params.quizId}}</p>
				<div class="field save-button-field">
					<div class="control">
						<button class="button is-primary" @click="onFormSave">Zapisz</button>
					</div>
				</div>
			</div>
			<div class="field question-field">
				<div class="control">
					<span>Pytanie</span>
					<wnl-quill
						ref="questionEditor"
						name="question"
						:options="{ theme: 'snow', placeholder: 'Pytanie' }"
						:allowMentions=false
						@input="onInput"
					/>
				</div>
			</div>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Tagi</legend>
				<wnl-tags :defaultTags="questionTags" ref="tags"></wnl-tags>
			</fieldset>
			<fieldset class="question-form-fieldset">
				<label class="label checkbox-label">
						<span>Czy zagwarantować kolejność pytań?</span>
						<input
							type="checkbox"
							name="preserveOrder"
							class="preserve-order"
							checked="preserveOrder"
						>
					</label>
			</fieldset>
			<div
				class="field answer-field"
				v-for="(answer, index) in questionAnswers"
				:data-id="answer.id"
				v-bind:key="answer.id"
			>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Odpowiedź {{index + 1}}</legend>
				<div class="control answer-control">
					<label class="label checkbox-label">
						<span>Prawidłowa?</span>
						<input
							type="checkbox"
							:name="'is_correct.' + answer.id"
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
			</fieldset>
			</div>
		</wnl-form>
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

	.question-form-header
		display: flex
		justify-content: space-between

	.question-field
		margin-bottom: $margin-huge

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

	.question-form-fieldset
		border: $border-light-gray
		padding: 10px 15px
		margin: 15px 0

	.question-form-legend
		font-size: 1.25rem
</style>

<script>
	import { mapActions, mapGetters } from 'vuex';
	import { Quill, Form, Tags } from 'js/components/global/form'
	import { nextTick } from 'vue'

	export default {
		name: 'QuizesEditor',
		components: {
			'wnl-form': Form,
			'wnl-quill': Quill,
			'wnl-tags': Tags
		},
		data: function () {
			return {
				questionQuillContent: '',
				attach: null
			}
		},
		props: ['isEdit'],
		computed: {
			...mapGetters([
				'questionText',
				'questionAnswers',
				'questionAnswersMap',
				'questionId',
				'questionTags',
				'preserveOrder'
			]),
			formResourceRoute() {
				if (!this.isEdit) {
					return 'quiz_questions?include=quiz_answers'
				} else {
					return `quiz_questions/${this.$route.params.quizId}?include=quiz_answers`
				}
			},
			formMethod() {
				return this.isEdit ? 'put' : 'post'
			}
		},
		methods: {
			...mapActions([
				'getQuizQuestion',
				'setupFreshQuestion'
			]),
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
				// This way we can attach answers and tags
				this.attach = this.getAttachedData()
			},
			getAttachedData() {
				const attachedData = {};
				const answerFields = this.$el.querySelectorAll('.answer-field')
				const answerFieldsArray = Array.prototype.slice.call(answerFields)

				const answersData = answerFieldsArray
					.map(field => ({
						id: field.dataset.id,
						text: field.querySelector('.answer-text').value,
						is_correct: field.querySelector('.answer-correct').checked,
					}))

				attachedData.answers = answersData;

				if (this.$refs.tags.haveTagsChanged()) {
					attachedData.tags = this.$refs.tags.tags
				}

				attachedData['preserve_order'] = this.$el.querySelector('preserve-order').checked

				return attachedData
			},
			onSubmitSuccess(data) {
				if (this.isEdit) {
					this.getQuizQuestion($route.params.quizId)
				} else {
					this.$router.push({name: 'quiz-editor', params: { quizId: data.id }})
				}
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
			if (this.isEdit) {
				this.getQuizQuestion(this.$route.params.quizId)
			} else {
				this.setupFreshQuestion()
			}
		},
	}
</script>
