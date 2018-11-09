<template>
	<div class="flashcard-editor">
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

</style>

<script>
	import _ from 'lodash'

	import Form from 'js/classes/forms/Form'
	import { getApiUrl } from 'js/utils/env'
	import { alerts } from 'js/mixins/alerts'

	import Textarea from "js/admin/components/forms/Textarea";

	export default {
		name: 'FlashcardEditor',
		components: {
			'wnl-form-textarea': Textarea,
		},
		mixins: [ alerts ],
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
				return this.$route.params.flashcardId !== 'new';
			},
			flashcardResourceUrl() {
				return getApiUrl(this.isEdit ? `flashcards/${this.$route.params.flashcardId}` : 'flashcards')
			},
			hasChanged() {
				return !_.isEqual(this.form.originalData, this.form.data())
			}
		},
		methods: {
			flashcardFormSubmit() {
				if (!this.hasChanged) {
					return false
				}

				this.loading = true
				this.form[this.isEdit ? 'put' : 'post'](this.flashcardResourceUrl)
					.then(response => {
						this.loading = false
						this.successFading('Pytanie zapisane!', 2000)
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
			this.form.populate(this.flashcardResourceUrl)
		}
	}
</script>
