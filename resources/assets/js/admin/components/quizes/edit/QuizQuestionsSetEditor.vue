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
			>Nazwa
			</wnl-form-input>
			<label class="label">Lekcja, której dotyczy zestaw</label>
			<span class="select quiz-questions-set-editor-select">
				<wnl-select :form="form"
					:options="lessonsOptions"
					v-model="form.lesson_id"
					name="lesson_id"
				/>
			</span>
			<label class="label">Opis</label>
			<wnl-quill
				ref="descriptionEditor"
				name="description"
				:options="{ theme: 'snow', placeholder: 'Opis' }"
				:value="form.description"
				:upload-enabled="false"
				@input="onDescriptionInput"
			/>
			<h4 class="title margin top">Dodaj pytanie</h4>
			<input
				class="quiz-question-id-input"
				type="number"
				name="quizQuestionInput"
				v-model="quizQuestionInput"
				placeholder="Podaj numer id pytania"
			>
			<button class="button is-small is-success"
				type="button"
				name="button"
				@click="addQuizQuestion(quizQuestionInput)"
			>Dodaj
			</button>
			<h4 class="title margin top">Lista pytań</h4>
			<div class="quiz-questions-admin" v-if="form.questions">
				<draggable v-model="form.questions" @start="drag=true" @end="drag=false">
					<wnl-quiz-questions-set-list-item
						v-for="questionId in form.questions"
						:key="questionId"
						:id="questionId"
						:content="getQuizQuestionContent(questionId)"
						@remove="removeQuestion(questionId)"
					/>
				</draggable>
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

	.quiz-question-id-input
		border: 0
		border-bottom: 1px solid $color-ocean-blue
		border-radius: 0
		box-shadow: none
		font-size: $font-size-plus-1
		font-weight: bold
		margin-top: $margin-small
		outline: 0
		text-align: center
		width: 250px

</style>

<script>
import {isEqual} from 'lodash';
import {mapGetters, mapActions} from 'vuex';
import draggable from 'vuedraggable';

import Form from 'js/classes/forms/Form';
import {getApiUrl} from 'js/utils/env';

import WnlAutocomplete from 'js/components/global/Autocomplete';
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
			quizQuestionInput: 0,
			loading: false,
		};
	},
	computed: {
		...mapGetters('lessons', ['allLessons']),
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
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', {setupLessons: 'setup'}),
		getQuizQuestionContent(questionId) {
			let question = Object.values(this.form.included.questions).find(question => question.id === questionId);

			if (question) {
				return question.text;
			} else {
				return 'Nowo dodane pytanie';
			}
		},
		populateForm() {
			this.form.populate(this.quizQuestionsSetResourceUrl);
		},
		onDescriptionInput() {
			this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
		},
		removeQuestion(questionId) {
			this.form.questions = this.form.questions.filter(id => id !== questionId);
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
					this.populateForm();
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
		addQuizQuestion(questionId) {
			let parsedQuestionId = parseInt(questionId);

			if (!Number.isInteger(parsedQuestionId)) {
				return this.addAutoDismissableAlert({
					text: 'Podana wartość nie jest numerem!',
					type: 'error'
				});
			} else if (parsedQuestionId === 0) {
				return this.addAutoDismissableAlert({
					text: 'Podana wartość nie może być zerem!',
					type: 'error'
				});
			}

			if (!this.form.questions.find(id => id === parsedQuestionId)) {
				this.form.questions.push(parsedQuestionId);
				this.addAutoDismissableAlert({
					text: 'Udało się dodać pytanie :)',
					type: 'success'
				});
				this.quizQuestionsSetFormSubmit();
			} else {
				this.addAutoDismissableAlert({
					text: 'Pytanie o tym numerze id znajduje się już w tym zestawie!',
					type: 'error'
				});
			}
			this.quizQuestionInput = 0;
		}
	},
	mounted() {
		if (this.isEdit) {
			this.populateForm();
		}
		this.setupLessons();
	},
};
</script>
