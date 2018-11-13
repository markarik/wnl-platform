<template>
	<div class="flashcards-set-editor">
		<form @submit.prevent="flashcardsSetFormSubmit">
			<div class="flashcards-set-editor-header">
				<h3 class="title">
					Edycja zestawu pytań
					<span v-if="isEdit">(Id: {{flashcardsSetId}})</span>
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
			<wnl-form-input
					name="mind_maps_text"
					:form="form"
					v-model="form.mind_maps_text"
			>
				Mind mapy
			</wnl-form-input>
			<label class="label">Lekcja, której dotyczy zestaw</label>
			<span class="select flashcards-set-editor-select">
				<wnl-select :form="form"
							 :options="lessonsOptions"
							 name="lesson_id"
							 v-model="form.lesson_id"
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
			<div class="flashcards-admin">
				<draggable v-model="form.flashcards" @start="drag=true" @end="drag=false" v-if="areFlashcardsReady">
					<wnl-flashcards-set-list-item
							v-for="flashcardId in form.flashcards"
							:key="flashcardId"
							:id="flashcardId"
							:content="allFlashcards.find(flashcard => flashcard.id === flashcardId).content"
							@remove="removeFlashcard(flashcardId)"
					/>
				</draggable>

				<form @submit.prevent="questionFormSubmit">
					<wnl-form-input
							name="flashcardId"
							:form="flashcard"
							v-model="flashcard.flashcardId"
					>
						Dodaj pytanie (Id pytania)
					</wnl-form-input>
					<button class="button is-small is-success"
							type="submit"
							:disabled="!flashcard.flashcardId"
					>
						<span class="margin right">+ Dodaj pytanie</span>
					</button>
				</form>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.flashcards-set-editor
		max-width: 800px
	.flashcards-set-editor-header
		+small-shadow-bottom()
		align-items: flex-start
		background: $color-white
		display: flex
		justify-content: space-between
		margin-bottom: $margin-medium
		padding-top: $margin-small
		position: sticky
		top: -$margin-big
		z-index: 1
	.flashcards-admin
		display: flex
	.flashcards-set-editor-select
		display: block
		/deep/ select
			width: 100%

</style>

<script>
	import { isEqual } from 'lodash';
	import { mapGetters, mapActions } from 'vuex';
	import draggable from 'vuedraggable';

	import Form from 'js/classes/forms/Form'
	import {getApiUrl} from 'js/utils/env'

	import WnlFormTextarea from "js/admin/components/forms/Textarea";
	import WnlFormInput from "js/admin/components/forms/Input";
	import WnlQuill from 'js/admin/components/forms/Quill';
	import WnlSelect from 'js/admin/components/forms/Select';
	import WnlFlashcardsSetListItem from 'js/admin/components/flashcards/edit/FlashcardsSetListItem';

	export default {
		name: 'FlashcardsSetEditor',
		components: {
			WnlFormInput,
			WnlQuill,
			WnlFormTextarea,
			WnlSelect,
			WnlFlashcardsSetListItem,
			draggable,
		},
		props: ['flashcardsSetId'],
		data() {
			return {
				form: new Form({
					name: null,
					description: null,
					mind_maps_text: null,
					lesson_id: null,
					flashcards: [],
				}),
				flashcard: new Form({
					flashcardId: null,
				}),
				loading: false,
			}
		},
		computed: {
			...mapGetters('lessons', ['allLessons']),
			...mapGetters('flashcards', {
				allFlashcards: 'allFlashcards',
				areFlashcardsReady: 'isReady'
			}),
			lessonsOptions() {
				return this.allLessons.map(lesson => ({
					text: lesson.name,
					value: lesson.id,
				}));
			},
			isEdit() {
				return this.flashcardsSetId !== 'new';
			},
			flashcardsSetResourceUrl() {
				return getApiUrl(this.isEdit ? `flashcards_sets/${this.flashcardsSetId}?include=flashcards` : 'flashcards_sets');
			},
			hasChanged() {
				return !isEqual(this.form.originalData, this.form.data());
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			...mapActions('lessons', { setupLessons: 'setup' }),
			...mapActions('flashcards', { setupFlashcards: 'setup' }),
			onDescriptionInput() {
				this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
			},
			removeFlashcard(flashcardId) {
				this.form.flashcards = this.form.flashcards.filter(id => id !== flashcardId)
			},
			flashcardsSetFormSubmit() {
				if (!this.hasChanged) {
					return false;
				}

				this.loading = true;
				this.form[this.isEdit ? 'put' : 'post'](this.flashcardsSetResourceUrl)
					.then(response => {
						this.loading = false;
						this.addAutoDismissableAlert({
							text: 'Zestaw pytań zapisany!',
							type: 'success'
						});
						this.form.originalData = this.form.data()
					})
					.catch(exception => {
						this.loading = false;
						this.addAutoDismissableAlert({
							text: 'Nie udało się :(',
							type: 'error'
						});
						$wnl.logger.capture(exception)
					})
			},
			questionFormSubmit() {
				// TODO flashcard autocomplete

				const flashcardId = parseInt(this.flashcard.flashcardId, 10);
				if (this.form.flashcards.includes(flashcardId)) {
					this.addAutoDismissableAlert({
						text: 'To pytanie jest już w zestawie',
						type: 'error'
					});
				} else {
					this.form.flashcards.push(flashcardId);
				}
				this.flashcard.reset();
			}
		},
		mounted() {
			this.form.populate(this.flashcardsSetResourceUrl);
			this.setupLessons();
			this.setupFlashcards();
		}
	}
</script>
