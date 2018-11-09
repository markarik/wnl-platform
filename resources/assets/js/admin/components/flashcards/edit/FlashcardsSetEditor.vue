<template>
	<div class="flashcards-set-editor">
		<wnl-alert v-for="(alert, timestamp) in alerts"
				   :alert="alert"
				   cssClass="fixed"
				   :key="timestamp"
				   :timestamp="timestamp"
				   @delete="onDelete"
		></wnl-alert>
		<form @submit.prevent="flashcardsSetFormSubmit">
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
			<label class="label">Lekcja</label>
			<wnl-select :form="form"
						 :options="lessons"
						 name="lesson_id"
						 v-model="form.lesson_id"
			/>
			<label class="label">Opis</label>
			<wnl-quill
				ref="descriptionEditor"
				name="description"
				:options="{ theme: 'snow', placeholder: 'Opis' }"
				:value="form.description"
				@input="onDescriptionInput"
			/>
			<div class="control">
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
			<form @submit.prevent="QuestionFormSubmit">
				<wnl-form-input
					name="flashcardId"
					:form="flashcard"
					v-model="flashcard.flashcardId"
				>
				Id pytania
				</wnl-form-input>
				<button class="button is-small is-success"
						type="submit"
				>
					<span class="margin right">+ Dodaj pytanie</span>
					<span class="icon is-small">
						<i class="fa fa-save"></i>
					</span>
				</button>
			</form>
			<div v-for="flashcardId in form.flashcards" v-if="areFlashcardsReady" :key="flashcardId">
				{{flashcardId}}. {{allFlashcards.find(flashcard => flashcard.id === flashcardId).content}}
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import Form from 'js/classes/forms/Form'
	import {getApiUrl} from 'js/utils/env'
	import {alerts} from 'js/mixins/alerts'

	import WnlFormTextarea from "js/admin/components/forms/Textarea";
	import WnlFormInput from "js/admin/components/forms/Input";
	import WnlQuill from 'js/admin/components/forms/Quill';
	import WnlSelect from 'js/admin/components/forms/Select';

	export default {
		name: 'FlashcardsSetEditor',
		components: {
			WnlFormInput,
			WnlQuill,
			WnlFormTextarea,
			WnlSelect
		},
		mixins: [alerts],
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
			lessons() {
				return this.allLessons.map(lesson => ({
					text: lesson.name,
					value: lesson.id,
				}));
			},
			isEdit() {
				return this.$route.params.flashcardsSetId !== 'new';
			},
			flashcardsSetResourceUrl() {
				return getApiUrl(this.isEdit ? `flashcards_sets/${this.$route.params.flashcardsSetId}?include=flashcards` : 'flashcards_sets')
			},
			hasChanged() {
				return !_.isEqual(this.form.originalData, this.form.data())
			}
		},
		methods: {
			...mapActions('lessons', { setupLessons: 'setup' }),
			...mapActions('flashcards', { setupFlashcards: 'setup' }),
			onDescriptionInput() {
				this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
			},
			flashcardsSetFormSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true
				this.form[this.isEdit ? 'put' : 'post'](this.flashcardsSetResourceUrl)
					.then(response => {
						this.loading = false
						this.successFading('Zestaw pytań zapisany!', 2000)
						this.form.originalData = this.form.data()
					})
					.catch(exception => {
						this.loading = false
						this.errorFading('Nie udało się :(', 2000)
						$wnl.logger.capture(exception)
					})
			},
			QuestionFormSubmit() {
				const flashcardId = parseInt(this.flashcard.flashcardId, 10);
				if (this.form.flashcards.includes(flashcardId)) {
					this.errorFading('To pytanie jest już w zestawie', 2000)
				} else {
					this.form.flashcards.push(flashcardId);
				}
				this.flashcard.flashcardId = null;
			}
		},
		mounted() {
			this.form.populate(this.flashcardsSetResourceUrl);
			this.setupLessons();
			this.setupFlashcards();
		}
	}
</script>
