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

			<wnl-form-input type="text" name="old_password" :form="form" v-model="form.old_password"></wnl-form-input>
			<wnl-form-input type="text" name="new_password" :form="form" v-model="form.new_password"></wnl-form-input>
			<wnl-form-input type="text" name="new_password_confirmation" :form="form" v-model="form.new_password_confirmation"></wnl-form-input>

			<p class="has-text-centered">
				<button class="button is-primary" :disabled="form.errors.any()">Zapisz</button>
			</p>
		</form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import Form from '../../classes/forms/Form'
	import Input from '../global/form/Input'

	export default {
		data() {
			return {
				form: new Form({
					old_password: null,
					new_password: null,
					new_password_confirmation: null,
					resourceUrl: '/papi/v1/users/current/password',
				}),
				saved: false,
				submissionFailed: false
			}
		},
		methods: {
			onSubmit() {
				this.form.put(this.form.resourceUrl)
						.then(response => this.saved = true)
						.catch(exception => {
							this.submissionFailed = true
							$wnl.logger.capture(exception)
						})
			}
		},
		components: {
			'wnl-form-input': Input,
		},
	}
</script>
