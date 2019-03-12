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
			<div class="quiz-questions-admin" v-if="form.quiz_questions && formPopulated">
				<wnl-draggable v-model="form.quiz_questions" @start="drag=true" @end="drag=false">
					<wnl-quiz-questions-set-list-item
						v-for="questionId in form.quiz_questions"
						:key="questionId"
						:id="questionId"
						:content="getQuizQuestionContent(questionId)"
						@remove="removeQuestion(questionId)"
					/>
				</wnl-draggable>
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
import {isEqual, isEmpty} from 'lodash';
import {mapState, mapActions} from 'vuex';
import WnlDraggable from 'vuedraggable';
import {ALERT_TYPES} from 'js/consts/alert';

import Form from 'js/classes/forms/Form';
import {getApiUrl} from 'js/utils/env';

import WnlFormInput from 'js/admin/components/forms/Input';
import WnlQuill from 'js/admin/components/forms/Quill';
import WnlSelect from 'js/admin/components/forms/Select';
import WnlQuizQuestionsSetListItem from 'js/admin/components/quizes/edit/QuizQuestionsSetListItem';

export default {
	components: {
		WnlFormInput,
		WnlQuill,
		WnlSelect,
		WnlQuizQuestionsSetListItem,
		WnlDraggable,
	},
	props: ['quizQuestionsSetId'],
	data() {
		return {
			form: new Form({
				name: '',
				description: '',
				lesson_id: null,
				quiz_questions: [],
			}),
			quizQuestionsObjects: {},
			quizQuestionInput: '',
			loading: false,
			formPopulated: false,
		};
	},
	computed: {
		...mapState('lessons', ['lessons']),
		lessonsOptions() {
			return this.lessons.map(lesson => ({
				text: lesson.name,
				value: lesson.id,
			}));
		},
		isEdit() {
			return this.quizQuestionsSetId !== 'new';
		},
		quizQuestionsSetResourceUrl() {
			return getApiUrl(this.isEdit ? `quiz_sets/${this.quizQuestionsSetId}?include=quiz_questions` : 'quiz_sets');
		},
		hasChanged() {
			return !isEqual(this.form.originalData, this.form.data());
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('lessons', {setupLessons: 'setup'}),
		getQuizQuestionContent(questionId) {
			return Object.values(this.quizQuestionsObjects).find(question => question.id === questionId).text;
		},
		async populateForm() {
			const fetchedForm = await this.form.populate(this.quizQuestionsSetResourceUrl);

			if (fetchedForm.included && fetchedForm.included.quiz_questions) {
				this.quizQuestionsObjects = {...fetchedForm.included.quiz_questions};
			} else {
				this.quizQuestionsObjects = {};
			}

			this.formPopulated = true;
		},
		onDescriptionInput() {
			this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
		},
		removeQuestion(questionId) {
			let questions = this.form.quiz_questions;

			let index = questions.findIndex(id => id === questionId);

			if (index > -1) {
				questions.splice(index, 1);
			}
		},
		quizQuestionsSetFormSubmit() {
			if (!this.hasChanged) {
				return false;
			} else if (!this.form.lesson_id) {
				return this.addAutoDismissableAlert({
					text: 'Lekcja, której dotyczy zestaw nie może być pustym polem.',
					type: ALERT_TYPES.ERROR
				});
			} else if (isEmpty(this.form.quiz_questions)) {
				return this.addAutoDismissableAlert({
					text: 'Zestaw musi posiadać chociaż jedno pytanie.',
					type: ALERT_TYPES.ERROR
				});
			}

			this.loading = true;

			this.form[this.isEdit ? 'put' : 'post'](this.quizQuestionsSetResourceUrl)
				.then(response => {
					this.addAutoDismissableAlert({
						text: 'Zestaw pytań zapisany!',
						type: ALERT_TYPES.SUCCESS
					});

					if (!this.isEdit) {
						this.$router.push({
							name: 'quiz-sets-edit',
							params: {
								quizQuestionsSetId: response.id
							}
						});
					} else {
						this.populateForm();
					}
				})
				.catch(exception => {
					this.addAutoDismissableAlert({
						text: 'Nie udało się :(',
						type: ALERT_TYPES.ERROR
					});
					$wnl.logger.capture(exception);
				})
				.finally(() => {
					this.loading = false;
				});
		},
		async addQuizQuestion(questionId) {
			let parsedQuestionId = parseInt(questionId);

			if (!Number.isInteger(parsedQuestionId)) {
				return this.addAutoDismissableAlert({
					text: 'Podana wartość nie jest numerem!',
					type: ALERT_TYPES.ERROR
				});
			} else if (parsedQuestionId === 0) {
				return this.addAutoDismissableAlert({
					text: 'Podana wartość nie może być zerem!',
					type: ALERT_TYPES.ERROR
				});
			}

			if (!this.form.quiz_questions.find(id => id === parsedQuestionId)) {
				try {
					const fetchedQuestion = await axios.get(getApiUrl(`quiz_questions/${questionId}`));
					this.quizQuestionsObjects[fetchedQuestion.data.id] = fetchedQuestion.data;
					this.form.quiz_questions.push(fetchedQuestion.data.id);

					this.addAutoDismissableAlert({
						text: 'Udało się dodać pytanie :)',
						type: ALERT_TYPES.SUCCESS
					});
				} catch (error) {
					if (error.response.status === 404) {
						this.addAutoDismissableAlert({
							text: 'Nie ma pytania o podanym numerze id!',
							type: ALERT_TYPES.ERROR
						});
					} else {
						this.addAutoDismissableAlert({
							text: 'Nie udało się zapisać pytania!',
							type: ALERT_TYPES.ERROR
						});
					}
				}
			} else {
				this.addAutoDismissableAlert({
					text: 'Pytanie o tym numerze id znajduje się już w tym zestawie!',
					type: ALERT_TYPES.ERROR
				});
			}
			this.quizQuestionInput = '';
		},
	},
	mounted() {
		if (this.isEdit) {
			this.populateForm();
		} else {
			this.formPopulated = true;
		}
		this.setupLessons();
	},
};
</script>
