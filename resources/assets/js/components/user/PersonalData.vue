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
		<wnl-user-address />
	</div>
</template>

<style lang="sass">

</style>

<script>
import IdentityNumber from 'js/components/user/IdentityNumber';
import WnlUserAddress from 'js/components/user/UserAddress';

export default {
	data() {
		return {
			personalDataExplanation: this.$t('user.personalData.header.explanation')
		};
	},
	components: {
		WnlUserAddress,
		'wnl-identity-number': IdentityNumber,
	}
};
</script>
