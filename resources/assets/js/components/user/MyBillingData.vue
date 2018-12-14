<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dane do faktury
				</div>
			</div>
		</div>

		<wnl-form class="margin vertical" name="MyBillingData" method="put" :resourceRoute="resourceRoute" populate="true">
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
import { Form, Text } from 'js/components/global/form';

export default {
	components: {
		'wnl-form': Form,
		'wnl-form-text': Text,
	},
	props: {
		resourceRoute: {
			type: String,
			default: 'users/current/billing'
		}
	}
};
</script>
