<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.address.address') }}
				</div>
			</div>
		</div>
		<wnl-form
			class="margin vertical"
			method="put"
			name="MyAddress"
			:resource-route="resourceRoute"
			:populate="!address"
			:value="address"
		>
			<wnl-form-text name="recipient">{{ $t('user.address.recipient') }}</wnl-form-text>
			<wnl-form-text name="street">{{ $t('user.address.street') }}</wnl-form-text>
			<wnl-form-text name="zip">{{ $t('user.address.zip') }}</wnl-form-text>
			<wnl-form-text name="city">{{ $t('user.address.city') }}</wnl-form-text>
			<wnl-form-text name="phone">{{ $t('user.address.phone') }}</wnl-form-text>
		</wnl-form>
	</div>
</template>

<style lang="sass">

</style>

<script>
import { Form, Text } from 'js/components/global/form';

export default {
	props: {
		resourceRoute: {
			type: String,
			default: 'users/current/address',
		},
		address: {
			type: Object,
		},
	},
	components: {
		'wnl-form': Form,
		'wnl-form-text': Text,
	},
};
</script>
