<template>
	<div class="quiz-question-editor">
		<wnl-form
			:attach="attach"
			class="chat-message-form"
			hideDefaultSubmit="true"
			name="QuizQuestionEditor"
			:method="formMethod"
			:suppress-enter="true"
			:resourceRoute="formResourceRoute"
			@submitSuccess="onSubmitSuccess"
			ref="quizQuestionForm"
		>
			<header class="question-form-header">
				<h4 v-if="isEdit">
					Edycja pytania
					<strong>{{$route.params.quizId}}</strong>
					<strong class="has-text-danger" v-if="questionIsDeleted">Usunięte</strong>
				</h4>
				<h4 v-else>Tworzenie nowego pytania</h4>
				<div class="field save-button-field">
					<div class="control">
						<button
								v-if="isEdit && !questionIsDeleted"
								class="button is-danger"
								type="button"
								@click="onDelete"
						>
							<span class="icon"><i class="fa fa-trash"></i></span>
							<span>Usuń</span>
						</button>
						<button
								v-if="isEdit && questionIsDeleted"
								class="button is-warning"
								type="button"
								@click="onUndelete"
						>
							<span class="icon"><i class="fa fa-undo"></i></span>
							<span>Przywróć</span>
						</button>
						<button class="button is-primary" @click.stop.prevent="onFormSave">Zapisz</button>
					</div>
				</div>
			</header>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Pytanie</legend>
				<div class="field question-field">
					<div class="control">
						<wnl-quill
							ref="questionEditor"
							name="question"
							:options="{ theme: 'snow', placeholder: 'Pytanie' }"
							@input="onQuestionInput"
						/>
					</div>
				</div>
			</fieldset>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Wyjaśnienie</legend>
				<div class="field question-field">
					<div class="control">
						<wnl-quill
							ref="explanationEditor"
							name="explanation"
							:options="{ theme: 'snow', placeholder: 'Wyjaśnienie' }"
							@input="onExplanationInput"
						/>
					</div>
				</div>
			</fieldset>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Tagi</legend>
				<wnl-tags :defaultTags="questionTags" ref="tags"></wnl-tags>
			</fieldset>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Powiązane slajdy</legend>
				<wnl-slide-ids :defaultSlides="questionSlides" ref="slides"></wnl-slide-ids>
			</fieldset>
			<fieldset class="question-form-fieldset">
				<label class="label checkbox-label">
						<span>Czy zagwarantować kolejność odpowiedzi ?</span>
						<input
							type="checkbox"
							name="preserveOrder"
							class="preserve-order"
							:checked="preserveOrder"
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
import { Quill, Form, Tags, SlideIds } from 'js/components/global/form';
import { nextTick } from 'vue';
import _ from 'lodash';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'QuizesEditor',
	components: {
		'wnl-form': Form,
		'wnl-quill': Quill,
		'wnl-tags': Tags,
		'wnl-slide-ids': SlideIds
	},
	data: function () {
		return {
			questionQuillContent: '',
			explanationQuillContent: '',
			attach: null
		};
	},
	props: ['isEdit'],
	computed: {
		...mapGetters([
			'questionText',
			'questionExplanation',
			'questionAnswers',
			'questionSlides',
			'questionAnswersMap',
			'questionId',
			'questionIsDeleted',
			'questionTags',
			'preserveOrder'
		]),
		formResourceRoute() {
			if (!this.isEdit) {
				return 'quiz_questions?include=quiz_answers';
			} else {
				return `quiz_questions/${this.$route.params.quizId}?include=quiz_answers`;
			}
		},
		formMethod() {
			return this.isEdit ? 'put' : 'post';
		}
	},
	methods: {
		...mapActions([
			'getQuizQuestion',
			'deleteQuizQuestion',
			'undeleteQuizQuestion',
			'setupFreshQuestion',
			'addAutoDismissableAlert',
		]),
		onQuestionInput() {
			this.questionQuillContent = this.$refs.questionEditor.editor.innerHTML;
		},
		onExplanationInput() {
			this.explanationQuillContent = this.$refs.explanationEditor.editor.innerHTML;
		},
		onRightAnswerChange(evt) {
			const previouslyChecked = this.$el.querySelectorAll('.answer-correct');

			Array.prototype.forEach.call(previouslyChecked, checkbox => {
				if (checkbox !== evt.target) {
					checkbox.checked = false;
				}
			});
		},
		onFormSave() {
			// This way we can attach answers and tags
			this.attach = this.getAttachedData();
			this.$nextTick(() => this.$refs.quizQuestionForm.onSubmitForm());
		},
		getAttachedData() {
			const attachedData = {};
			const answerFields = this.$el.querySelectorAll('.answer-field');
			const answerFieldsArray = Array.prototype.slice.call(answerFields);

			const answersData = answerFieldsArray
				.map(field => ({
					id: field.dataset.id,
					text: field.querySelector('.answer-text').value,
					is_correct: field.querySelector('.answer-correct').checked,
				}));

			attachedData.answers = answersData;

			if (this.$refs.tags.haveTagsChanged()) {
				attachedData.tags = this.$refs.tags.tags;
			}

			if (this.$refs.slides.haveSlidesChanged()) {
				attachedData.slides = _.map(this.$refs.slides.slides, slide => slide.id);
			}

			attachedData['preserve_order'] = this.$el.querySelector('.preserve-order').checked;

			return attachedData;
		},
		onSubmitSuccess(data) {
			if (this.isEdit) {
				this.getQuizQuestion(this.$route.params.quizId);
			} else {
				//Timeout for the user to see the success banner
				setTimeout(() => {
					this.$router.push({name: 'quiz-editor', params: { quizId: data.id }});
				}, 2000);
			}
		},
		async onDelete() {
			try {
				await this.deleteQuizQuestion(this.questionId);
				this.addAutoDismissableAlert({
					text: 'Poszło!',
					type: 'success',
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Niestety coś poszło nie tak :( Daj znać Wujowi',
					type: 'error',
				});
			}
		},
		async onUndelete() {
			try {
				await this.undeleteQuizQuestion(this.questionId);
				this.addAutoDismissableAlert({
					text: 'Poszło!',
					type: 'success',
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Niestety coś poszło nie tak :( Daj znać Wujowi',
					type: 'error',
				});
			}


		}

	},
	watch: {
		questionText(val) {
			if (val) this.$refs.questionEditor.editor.innerHTML = val;
		},
		questionExplanation(val) {
			if (val) this.$refs.explanationEditor.editor.innerHTML = val;
		},
		'$route.params.quizId'(quizId) {
			this.getQuizQuestion(this.$route.params.quizId);
		}
	},
	created() {
		if (this.isEdit) {
			this.getQuizQuestion(this.$route.params.quizId);
		} else {
			this.setupFreshQuestion();
		}
	},
};
</script>
