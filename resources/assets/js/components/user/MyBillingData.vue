<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dane do faktury
				</div>
			</div>
		</div>

		<wnl-form class="margin vertical" name="MyBillingData" method="put" resourceRoute="users/current/billing" populate="true">
			<wnl-form-text name="company_name">Nazwa firmy</wnl-form-text>
			<wnl-form-text name="vat_id">NIP</wnl-form-text>
			<wnl-form-text name="address">Adres</wnl-form-text>
			<wnl-form-text name="zip">Kod pocztowy</wnl-form-text>
			<wnl-form-text name="city">Miasto</wnl-form-text>
			<wnl-form-text name="country">Kraj</wnl-form-text>
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
