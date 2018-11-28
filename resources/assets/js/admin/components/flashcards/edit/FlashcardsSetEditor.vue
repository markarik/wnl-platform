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

				<div>
					<div class="control">
						<label class="label">Wybierz pytanie</label>
						<input class="input" placeholder="Id lub treść aby wyszukać" v-model="flashcardInput"/>
					</div>
					<div class="control">
						<wnl-autocomplete
								:isDown="true"
								:items="flashcardAutocompleteItems"
								:onItemChosen="addFlashcard"
								ref="autocomplete"
						>
							<template slot-scope="slotProps">
								<wnl-flashcard-autocomplete-item :item="slotProps.item" />
							</template>
						</wnl-autocomplete>
					</div>
				</div>
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
		z-index: 101

	.flashcards-admin
		display: flex

	.flashcards-set-editor-select
		display: block
		/deep/ select
			width: 100%

</style>

<script>
	import {isEqual} from 'lodash';
	import {mapGetters, mapActions, mapState} from 'vuex';
	import draggable from 'vuedraggable';

	import Form from 'js/classes/forms/Form'
	import {getApiUrl} from 'js/utils/env'

	import WnlAutocomplete from 'js/components/global/Autocomplete';
	import WnlFlashcardAutocompleteItem from 'js/admin/components/flashcards/edit/FlashcardAutocompleteItem'
	import WnlFormInput from "js/admin/components/forms/Input";
	import WnlQuill from 'js/admin/components/forms/Quill';
	import WnlSelect from 'js/admin/components/forms/Select';
	import WnlFlashcardsSetListItem from 'js/admin/components/flashcards/edit/FlashcardsSetListItem';

	export default {
		name: 'FlashcardsSetEditor',
		components: {
			WnlAutocomplete,
			WnlFormInput,
			WnlQuill,
			WnlSelect,
			WnlFlashcardsSetListItem,
			draggable,
			WnlFlashcardAutocompleteItem,
		},
		props: ['flashcardsSetId'],
		data() {
			return {
				form: new Form({
					name: '',
					description: '',
					mind_maps_text: '',
					lesson_id: null,
					flashcards: [],
				}),
				flashcardInput: '',
				loading: false,
			}
		},
		computed: {
			...mapGetters('lessons', ['allLessons']),
			...mapState('flashcards', {
				allFlashcards: 'flashcards',
				areFlashcardsReady: 'ready'
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
			},
			flashcardAutocompleteItems() {
				if (this.flashcardInput === '') {
					return [];
				}

				return this.allFlashcards
					.filter(flashcard => !this.form.flashcards.includes(flashcard.id) &&
						(
							flashcard.id === parseInt(this.flashcardInput, 10) ||
							flashcard.content.toLowerCase().includes(this.flashcardInput.toLowerCase())
						)
					)
					.slice(0, 10)
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			...mapActions('lessons', {setupLessons: 'setup'}),
			...mapActions('flashcards', {
				setupFlashcards: 'setup',
			}),
			...mapActions('flashcardsSets', {
				invalidateFlashcardsSetsCache: 'invalidateCache',
			}),
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
						this.invalidateFlashcardsSetsCache();
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
			addFlashcard(flashcard) {
				this.form.flashcards.push(flashcard.id);
				this.flashcardInput = '';
			}
		},
		mounted() {
			if (this.isEdit) {
				this.form.populate(this.flashcardsSetResourceUrl);
			}
			this.setupLessons();
			this.setupFlashcards();
		},
	}
</script>
