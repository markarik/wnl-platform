<template lang="html">
	<div class="scrollable-main-container">
		<h1>Adres</h1>
		<wnl-form name="MyAddress" resourceRoute="users/current/address">
			<wnl-form-text name="street">Ulica</wnl-form-text>
			<wnl-form-text name="zip">Kod pocztowy</wnl-form-text>
			<wnl-form-text name="city">Miasto</wnl-form-text>
			<wnl-form-text name="phone">Telefon</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { Form, Text } from 'js/components/global/form'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
		},
	}
</script>
