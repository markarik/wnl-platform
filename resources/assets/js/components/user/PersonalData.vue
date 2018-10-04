<template lang="html">
	<div class="scrollable-main-container">
		<div class="message is-primary">
			<div class="message-header">
				{{ $t('user.personalData.header.explanationHeader') }}
			</div>
			<div class="message-body" v-html="personalDataExplanation">
			</div>
		</div>
		<wnl-identity-number class="margin bottom"></wnl-identity-number>
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
			resourceRoute="users/current/address"
			populate="true"
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
	import { Form, Text } from 'js/components/global/form'
	import IdentityNumber from 'js/components/user/IdentityNumber'

	export default {
		data() {
			return {
				personalDataExplanation: this.$t('user.personalData.header.explanation')
			}
		},
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-identity-number': IdentityNumber,
		}
	}
</script>
