<template lang="html">
	<div class="container">
		<h1>Twój profil publiczny</h1>
		<wnl-avatar size="large"></wnl-avatar>
		<wnl-upload @success="onUploadSuccess">
			<a>Zmień</a>
		</wnl-upload>
		<div class="notification is-success has-text-centered" v-if="saved">
			Zapisano
		</div>
		<div class="notification is-danger has-text-centered" v-if="submissionFailed">
			Coś poszło nie tak...
		</div>

		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			  @keydown="form.errors.clear($event.target.name)">

			<wnl-form-input type="text" name="first_name" :form="form" v-model="form.first_name"></wnl-form-input>
			<wnl-form-input type="text" name="last_name" :form="form" v-model="form.last_name"></wnl-form-input>
			<wnl-form-input type="text" name="username" :form="form" v-model="form.username"></wnl-form-input>
			<wnl-form-input type="text" name="public_email" :form="form" v-model="form.public_email"></wnl-form-input>
			<wnl-form-input type="text" name="public_phone" :form="form" v-model="form.public_phone"></wnl-form-input>

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
	import Upload from '../global/Upload'
	import {mapActions} from 'vuex'

	export default {
		data() {
			return {
				form: new Form({
					first_name: null,
					last_name: null,
					public_email: null,
					public_phone: null,
					username: null,
				}),
				resourceUrl: '/papi/v1/users/current/profile',
				saved: false,
				submissionFailed: false
			}
		},
		methods: {
			...mapActions(['setupCurrentUser']),
			onSubmit() {
				this.form.put(this.resourceUrl)
						.then(response => this.saved = true)
						.catch(error => {
							this.submissionFailed = true
							// TODO: Push to sentry
						})
			},
			onUploadSuccess() {
				this.setupCurrentUser()
			}
		},
		mounted() {
			this.form.populate(this.resourceUrl)
		},
		components: {
			'wnl-form-input': Input,
			'wnl-upload': Upload,
		},
	}
</script>
