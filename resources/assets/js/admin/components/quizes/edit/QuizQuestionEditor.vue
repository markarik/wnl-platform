<template>
	<div class="quiz-question-editor">
		<wnl-form
			ref="quizQuestionForm"
			:attach="attach"
			class="chat-message-form"
			hide-default-submit="true"
			name="QuizQuestionEditor"
			:method="formMethod"
			:suppress-enter="true"
			:resource-route="formResourceRoute"
			@submitSuccess="onSubmitSuccess"
		>
			<header class="question-form-header">
				<h4 v-if="isEdit">
					Edycja pytania
						<strong>{{quizQuestionId}}</strong>
						<strong v-if="questionIsDeleted" class="has-text-danger">Usunięte</strong>
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
							<span class="icon"><i class="fa fa-trash" /></span>
							<span>Usuń</span>
						</button>
						<button
							v-if="isEdit && questionIsDeleted"
							class="button is-warning"
							type="button"
							@click="onUndelete"
						>
							<span class="icon"><i class="fa fa-undo" /></span>
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
				<wnl-tags ref="tags" :default-tags="questionTags" />
			</fieldset>
			<wnl-content-item-classifier-editor
				v-if="isEdit"
				class="margin bottom"
				:is-always-active="true"
				:content-item-id="quizQuestionId"
				:content-item-type="CONTENT_TYPES.QUIZ_QUESTION"
			/>
			<div v-else class="notification is-info">
				<span class="icon">
					<i class="fa fa-info-circle" />
				</span>
				Zapisz pytanie, aby przypisać do niego pojęcia
			</div>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Powiązane slajdy</legend>
				<wnl-slide-ids ref="slides" :default-slides="questionSlides" />
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
				v-for="(answer, index) in questionAnswers"
				:key="answer.id"
				class="field answer-field"
				:data-id="answer.id"
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
import _ from 'lodash';

import {
	Form as WnlForm,
	Quill as WnlQuill,
	SlideIds as WnlSlideIds,
	Tags as WnlTags,
} from 'js/components/global/form';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';

import { CONTENT_TYPES } from 'js/consts/contentClassifier';

export default {
	components: {
		WnlContentItemClassifierEditor,
		WnlForm,
		WnlQuill,
		WnlSlideIds,
		WnlTags,
	},
	props: {
		quizQuestionId: {
			type: [Number, String],
			default: null,
		}
	},
	data: function () {
		return {
			questionQuillContent: '',
			explanationQuillContent: '',
			attach: {},
			CONTENT_TYPES,
		};
	},
	computed: {
		...mapGetters([
			'questionText',
			'questionExplanation',
			'questionAnswers',
			'questionSlides',
			'questionAnswersMap',
			'questionIsDeleted',
			'questionTags',
			'preserveOrder'
		]),
		formResourceRoute() {
			if (this.isEdit) {
				return `quiz_questions/${this.quizQuestionId}?include=quiz_answers`;
			} else {
				return 'quiz_questions?include=quiz_answers';
			}
		},
		formMethod() {
			return this.isEdit ? 'put' : 'post';
		},
		isEdit() {
			return !!this.quizQuestionId;
		}
	},
	watch: {
		questionText(val) {
			if (val) this.$refs.questionEditor.editor.innerHTML = val;
		},
		questionExplanation(val) {
			if (val) this.$refs.explanationEditor.editor.innerHTML = val;
		},
		quizQuestionId(quizQuestionId) {
			this.getQuizQuestion(quizQuestionId);
			this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.QUIZ_QUESTION, contentIds: [quizQuestionId] });
		}
	},
	created() {
		if (this.isEdit) {
			this.getQuizQuestion(this.quizQuestionId);
		} else {
			this.setupFreshQuestion();
		}
	},
	async mounted() {
		if (this.isEdit) {
			await this.setupCurrentUser();
			await this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.QUIZ_QUESTION, contentIds: [this.quizQuestionId] });
		}
	},
	methods: {
		...mapActions([
			'getQuizQuestion',
			'deleteQuizQuestion',
			'undeleteQuizQuestion',
			'setupFreshQuestion',
			'addAutoDismissableAlert',
			'setupCurrentUser',
		]),
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
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
				this.getQuizQuestion(this.quizQuestionId);
			} else {
				//Timeout for the user to see the success banner
				setTimeout(() => {
					this.$router.push({ name: 'quiz-editor', params: { quizQuestionId: data.id } });
				}, 2000);
			}
		},
		async onDelete() {
			try {
				await this.deleteQuizQuestion(this.quizQuestionId);
				this.addAutoDismissableAlert({
					text: 'Poszło!',
					type: 'success',
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Niestety coś poszło nie tak :( Daj znać Damianowi',
					type: 'error',
				});
			}
		},
		async onUndelete() {
			try {
				await this.undeleteQuizQuestion(this.quizQuestionId);
				this.addAutoDismissableAlert({
					text: 'Poszło!',
					type: 'success',
				});
			} catch (error) {
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert({
					text: 'Niestety coś poszło nie tak :( Daj znać Damianowi',
					type: 'error',
				});
			}
		}
	},
};
</script>
