<template>
	<wnl-user-billing-data :resource-route="resourceRoute" />
</template>

<script>
import WnlUserBillingData from 'js/components/user/MyBillingData';

export default {
	components: {
		WnlUserBillingData
	},
	props: {
		address: {
			type: Object,
		},
		user: {
			type: Object,
			required: true
		}
	},
	computed: {
		resourceRoute() {
			return `users/${this.user.id}/billing`;
		}
	}
};
</script>
