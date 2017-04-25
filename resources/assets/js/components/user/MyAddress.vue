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

			<wnl-form-input type="text" name="address" :form="form" v-model="form.address"></wnl-form-input>
			<wnl-form-input type="text" name="zip" :form="form" v-model="form.zip"></wnl-form-input>
			<wnl-form-input type="text" name="city" :form="form" v-model="form.city"></wnl-form-input>
			<wnl-form-input type="text" name="phone" :form="form" v-model="form.phone"></wnl-form-input>

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
					address: null,
					zip: null,
					city: null,
					phone: null,
				}),
				resourceUrl: '/papi/v1/users/current/address',
				saved: false,
				submissionFailed: false
			}
		},
		methods: {
			onSubmit() {
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
			'wnl-form-input': Input,
		},
	}
</script>
