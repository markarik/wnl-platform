<template>
	<div class="flashcards-set-editor">
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
				}),
				loading: false,
			}
		},
		computed: {
			...mapGetters('lessons', ['allLessons']),
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
			}
		},
		mounted() {
			this.form.populate(this.flashcardsSetResourceUrl);
			this.setupLessons();
			this.setupFlashcards();
		}
	}
</script>
