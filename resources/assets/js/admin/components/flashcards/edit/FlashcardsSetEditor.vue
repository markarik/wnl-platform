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

	import Form from 'js/classes/forms/Form'
	import {getApiUrl} from 'js/utils/env'
	import {alerts} from 'js/mixins/alerts'

	import WnlFormTextarea from "js/admin/components/forms/Textarea";
	import WnlFormInput from "js/admin/components/forms/Input";
	import WnlQuill from 'js/admin/components/forms/Quill';

	export default {
		name: 'FlashcardsSetEditor',
		components: {
			WnlFormInput,
			WnlQuill,
			WnlFormTextarea,
		},
		mixins: [alerts],
		data() {
			return {
				form: new Form({
					name: null,
					description: null,
					mind_maps_text: null,
				}),
				loading: false,
			}
		},
		computed: {
			flashcardsSetResourceUrl() {
				return getApiUrl(`flashcards_sets/${this.$route.params.flashcardsSetId}`)
			},
			hasChanged() {
				return !_.isEqual(this.form.originalData, this.form.data())
			}
		},
		methods: {
			onDescriptionInput() {
				this.form.description = this.$refs.descriptionEditor.editor.innerHTML;
			},
			flashcardsSetFormSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true
				this.form.put(this.flashcardsSetResourceUrl)
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
			this.form.populate(this.flashcardsSetResourceUrl)
		}
	}
</script>
