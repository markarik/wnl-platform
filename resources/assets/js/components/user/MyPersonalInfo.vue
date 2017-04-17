<template lang="html">
	<div class="container">
		<h1>Twoje dane osobowe</h1>
		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			  @keydown="form.errors.clear($event.target.name)">
			<div class="field">
				<label for="first_name" class="label">Imię:</label>
				<input type="text" class="input is-medium" name="first_name" v-model="form.first_name" id="first_name">
				<span class="help is-danger" v-if="form.errors.has('first_name')"
					  v-text="form.errors.get('first_name')"></span>
			</div>
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

	export default {
		data() {
			return {
				form: new Form({
					first_name: '',
				})
			}
		},
		methods: {
			onSubmit() {
				this.form.put('/papi/v1/users/current')
						.then(response => alert('Wahoo!'));
			}
		}
	}
</script>
