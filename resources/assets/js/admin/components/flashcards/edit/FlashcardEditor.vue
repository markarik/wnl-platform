<template>
	<div class="flashcard-editor">
		<h3 class="title">
			Edycja pytania
			<span v-if="isEdit">Id: {{flashcardId}}</span>
		</h3>
		<form @submit.prevent="flashcardFormSubmit">
				<wnl-form-textarea
					name="content"
					:form="form"
					v-model="form.content"
				>
					Treść pytania
				</wnl-form-textarea>
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
	.flashcard-editor
		max-width: 800px
</style>

<script>
	import {isEqual} from 'lodash'
	import { mapActions } from 'vuex';

	import Form from 'js/classes/forms/Form'
	import { getApiUrl } from 'js/utils/env'

	import Textarea from "js/admin/components/forms/Textarea";

	export default {
		name: 'FlashcardEditor',
		components: {
			'wnl-form-textarea': Textarea,
		},
		props: ['flashcardId'],
		data() {
			return {
				form: new Form({
					content: null,
				}),
				loading: false,
			}
		},
		computed: {
			isEdit() {
				return this.flashcardId !== 'new';
			},
			flashcardResourceUrl() {
				return getApiUrl(this.isEdit ? `flashcards/${this.flashcardId}` : 'flashcards')
			},
			hasChanged() {
				return !isEqual(this.form.originalData, this.form.data())
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			...mapActions('flashcards', {
				invalidateFlashcardsCache: 'invalidateCache',
			}),
			flashcardFormSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true
				this.form[this.isEdit ? 'put' : 'post'](this.flashcardResourceUrl)
					.then(response => {
						this.loading = false
						this.addAutoDismissableAlert({
							text: 'Pytanie zapisane!',
							type: 'success',
						});
						this.invalidateFlashcardsCache();
						this.form.originalData = this.form.data()
					})
					.catch(exception => {
						this.loading = false
						this.addAutoDismissableAlert({
							text: 'Nie udało się :(',
							type: 'error',
						});
						$wnl.logger.capture(exception)
					})
			}
		},
		mounted() {
			this.form.populate(this.flashcardResourceUrl)
		}
	}
</script>
