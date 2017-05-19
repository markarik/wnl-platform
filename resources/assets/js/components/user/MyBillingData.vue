<template lang="html">
	<div>
		<h1>Dane do faktury</h1>

		<wnl-form name="MyBillingData" method="put" :resourceUrl="resourceUrl" populate="true">
			<wnl-form-text name="company_name">Nazwa firmy</wnl-form-text>
			<wnl-form-text name="vat_id">NIP</wnl-form-text>
			<wnl-form-text name="address">Adres</wnl-form-text>
			<wnl-form-text name="zip">Kod pocztowy</wnl-form-text>
			<wnl-form-text name="city">Miasto</wnl-form-text>
			<wnl-form-text name="country">Kraj</wnl-form-text>

			<span slot="submit-after">Zapisz</span>
		</wnl-form>

	</div>
</template>

<style lang="sass">

</style>

<script>
	import { Form, Text } from 'js/components/global/form'
	import { getApiUrl } from 'js/utils/env'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
		},
		computed: {
			resourceUrl() {
				return getApiUrl('users/current/billing')
			}
		},
	}
</script>
