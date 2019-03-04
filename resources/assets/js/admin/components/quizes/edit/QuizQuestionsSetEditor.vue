<template>
	<div class="quiz-questions-set-editor">
		<form @submit.prevent="quizQuestionsSetFormSubmit">
			<div class="quiz-questions-set-editor-header">
				<h3 class="title">
					Edycja zestawu pytań zamkniętych
					<span v-if="isEdit">(Id: {{quizQuestionsSetId}})</span>
				</h3>
				<button class="button is-small is-success"
						:class="{'is-loading': loading}"
						:disabled="!hasChanged"
						type="submit"
				>
					<span class="margin right">Zapisz</span>
					<span class="icon is-small">
					<i class="fa fa-save"></i>
				</span>
				</button>
			</div>
			<wnl-form-input
					name="name"
					:form="form"
					v-model="form.name"
			>
				Nazwa
			</wnl-form-input>
			<label class="label">Lekcja, której dotyczy zestaw</label>
			<span class="select quiz-questions-set-editor-select">
				<wnl-select :form="form"
							:options="lessonsOptions"
							name="lesson_id"
				/>
			</span>
			<label class="label">Opis</label>
			<wnl-quill
					ref="descriptionEditor"
					name="description"
					:options="{ theme: 'snow', placeholder: 'Opis' }"
					:value="form.description"
					@input="onDescriptionInput"
			/>
			<h4 class="title margin top">Lista pytań</h4>
			<div class="quiz-questions-admin" v-if="form.questions">
				<draggable v-model="form.questions" @start="drag=true" @end="drag=false">
					<wnl-quiz-questions-set-list-item
							v-for="questionId in form.questions"
							:key="questionId"
							:id="questionId"
							:content="getQuizQuestionContent(questionId)"
					/>
				</draggable>

				<wnl-autocomplete
					v-model="quizQuestionInput"
					:items="flashcardAutocompleteItems"
					@change="addQuizQuestion"
					placeholder="Id lub treść aby wyszukać"
					label="Wybierz pytanie"
				>
					<wnl-flashcard-autocomplete-item :item="slotProps.item" slot-scope="slotProps"/>
				</wnl-autocomplete>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.quiz-questions-set-editor
		max-width: 800px

	.quiz-questions-set-editor-header
		+small-shadow-bottom()
		align-items: flex-start
		background: $color-white
		display: flex
		justify-content: space-between
		margin-bottom: $margin-medium
		padding-top: $margin-small
		position: sticky
		top: -$margin-big
		z-index: 101

	.quiz-questions-admin
		display: flex
		align-items: flex-start

	.quiz-questions-set-editor-select
		display: block
		/deep/ select
			width: 100%

</style>

<script>
import {isEqual} from 'lodash';
import {mapGetters, mapActions, mapState} from 'vuex';
import draggable from 'vuedraggable';

import Form from 'js/classes/forms/Form';
import {getApiUrl} from 'js/utils/env';

import WnlAutocomplete from 'js/components/global/Autocomplete';
import WnlFlashcardAutocompleteItem from 'js/admin/components/flashcards/edit/FlashcardAutocompleteItem';
import WnlFormInput from 'js/admin/components/forms/Input';
import WnlQuill from 'js/admin/components/forms/Quill';
import WnlSelect from 'js/admin/components/forms/Select';
import WnlQuizQuestionsSetListItem from 'js/admin/components/quizes/edit/QuizQuestionsSetListItem';

export default {
	name: 'QuizQuestionsSetEditor',
	components: {
		WnlAutocomplete,
		WnlFormInput,
		WnlQuill,
		WnlSelect,
		WnlQuizQuestionsSetListItem,
		draggable,
		WnlFlashcardAutocompleteItem,
	},
	props: ['quizQuestionsSetId'],
	data() {
		return {
			form: new Form({
				name: '',
				description: '',
				lesson_id: null,
				questions: [],
			}),
			quizQuestionInput: '',
			loading: false,
		};
	},
	computed: {
		...mapGetters('lessons', ['allLessons']),
		...mapState('quiz', {
			allQuizQuestions: 'quizQuestions',
			areQuizQuestionsReady: 'ready'
		}),
		lessonsOptions() {
			return this.allLessons.map(lesson => ({
				text: lesson.name,
				value: lesson.id,
			}));
		},
		isEdit() {
			return this.quizQuestionsSetId !== 'new';
		},
		quizQuestionsSetResourceUrl() {
			return getApiUrl(this.isEdit ? `quiz_sets/${this.quizQuestionsSetId}?include=questions` : 'quiz_sets');
		},
		hasChanged() {
			return !isEqual(this.form.originalData, this.form.data());
		},
		flashcardAutocompleteItems() {
			if (this.quizQuestionInput === '') {
				return [];
			}

			return this.allQuizQuestions
				.filter(flashcard => !this.form.flashcards.includes(flashcard.id) &&
						(
							flashcard.id === parseInt(this.quizQuestionInput, 10) ||
							flashcard.content.toLowerCase().includes(this.quizQuestionInput.toLowerCase())
						)
				)
				.slice(0, 10);
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', {setupLessons: 'setup'}),
		...mapActions('quiz', {
			setupFlashcards: 'setup',
		}),
		...mapActions('flashcardsSets', {
			invalidateFlashcardsSetsCache: 'invalidateCache',
		}),
		getQuizQuestionContent(questionId) {
			return Object.values(this.form.included.questions).find(question => question.id === questionId).text
		},
		onDescriptionInput() {
			this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
		},
		removeFlashcard(flashcardId) {
			this.form.flashcards = this.form.flashcards.filter(id => id !== flashcardId);
		},
		quizQuestionsSetFormSubmit() {
			if (!this.hasChanged) {
				return false;
			}

			this.loading = true;
			this.form[this.isEdit ? 'put' : 'post'](this.quizQuestionsSetResourceUrl)
				.then(response => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Zestaw pytań zapisany!',
						type: 'success'
					});
					this.invalidateFlashcardsSetsCache();
					this.form.originalData = this.form.data();
				})
				.catch(exception => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie udało się :(',
						type: 'error'
					});
					$wnl.logger.capture(exception);
				});
		},
		addQuizQuestion(question) {
			this.form.questions.push(question.id);
			this.quizQuestionInput = '';
		}
	},
	mounted() {
		if (this.isEdit) {
			this.form.populate(this.quizQuestionsSetResourceUrl);
		}
		this.setupLessons();
		this.setupFlashcards();
	},
};
</script>
