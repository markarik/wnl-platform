<template>
    <div class="flashcard-editor">
        <form @submit.prevent="flashcardFormSubmit">
                <wnl-form-textarea
                    name="content"
                    :form="form"
                    v-model="form.content"
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
			flashcardResourceUrl() {
				return getApiUrl(`flashcards/${this.$route.params.flashcardId}`)
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
				this.form.put(this.flashcardResourceUrl)
					.then(response => {
						this.loading = false
						this.successFading('Powtórka zapisana!', 2000)
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
