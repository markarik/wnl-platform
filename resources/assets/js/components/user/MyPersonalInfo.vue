<template lang="html">
	<h1>Twoje dane osobowe</h1>
	<form class="" action="" method="POST">
		<input type="text" class="input" name="first-name" v-model="form.first_name">
	</form>
</template>

<style lang="sass">

</style>

<script>
	import Form from '../../classes/forms/Form'

	export default {
		data() {
			return {
				form: new Form({

				})
			}
		}
	}
</script>
