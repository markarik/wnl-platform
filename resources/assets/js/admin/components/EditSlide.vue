<template lang="html">
	<div class="container">
		<div class="notification is-success has-text-centered" v-if="saved">
			Zapisano
		</div>
		<div class="notification is-danger has-text-centered" v-if="submissionFailed">
			Coś poszło nie tak...
		</div>

		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			  @keydown="form.errors.clear($event.target.name)">

			<wnl-form-textarea type="text" name="content" :form="form" v-model="form.content"></wnl-form-textarea>
			<wnl-form-checkbox type="text" name="is_functional" :form="form" v-model="form.is_functional">
				Funkcjonalny
			</wnl-form-checkbox>

			<p class="has-text-centered">
				<button class="button is-primary" :disabled="form.errors.any()">Zapisz</button>
			</p>
		</form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import Form from 'js/classes/forms/Form'
	import Textarea from 'js/components/global/form/Textarea'
	import Checkbox from 'js/components/global/form/Checkbox'

	export default {
		props: ['id'],
		data() {
			return {
				form: new Form({
					content: null,
					is_functional: null,
				}),
				resourceUrl: '/papi/v1/slides/5',
				saved: false,
				submissionFailed: false
			}
		},
		methods: {
			onSubmit() {
				console.log(this.form.is_functional)
				this.form.put(this.resourceUrl)
						.then(response => this.saved = true)
						.catch(exception => {
							this.submissionFailed = true
							$wnl.logger.capture(exception)
						})
			}
		},
		mounted() {
			this.form.populate(this.resourceUrl)
		},
		components: {
			'wnl-form-checkbox': Checkbox,
			'wnl-form-textarea': Textarea,
		},
	}
</script>
