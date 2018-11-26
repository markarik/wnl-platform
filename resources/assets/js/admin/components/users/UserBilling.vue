<template lang="html">
	<div>
		<wnl-form
				class="margin vertical"
				method="put"
				name="BillingData"
				:resourceRoute="resourceRoute"
				:populate="!user.billing"
				:value="user.billing"
		>
			<wnl-form-text name="company_name">{{ $t('user.address.companyName') }}</wnl-form-text>
			<wnl-form-text name="vat_id">{{ $t('user.address.vatId') }}</wnl-form-text>
			<wnl-form-text name="address">{{ $t('user.address.street') }}</wnl-form-text>
			<wnl-form-text name="zip">{{ $t('user.address.zip') }}</wnl-form-text>
			<wnl-form-text name="city">{{ $t('user.address.city') }}</wnl-form-text>
			<wnl-form-text name="country">{{ $t('user.address.country') }}</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { Form, Text } from 'js/components/global/form'
	import IdentityNumber from 'js/components/user/IdentityNumber'

	export default {
		props: {
			address: {
				type: Object,
			},
			user: {
				type: Object,
				required: true
			}
		},
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
		},
        computed: {
			resourceRoute() {
				return `/users/${this.user.id}/user_billing_data`
			}
        }
	}
</script>
