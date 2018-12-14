<template lang="html">
	<wnl-user-billing-data :resource-route="resourceRoute"/>
</template>

<script>
import WnlUserBillingData from 'js/components/user/MyBillingData';

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
		WnlUserBillingData
	},
	computed: {
		resourceRoute() {
			return `users/${this.user.id}/billing`;
		}
	}
};
</script>
