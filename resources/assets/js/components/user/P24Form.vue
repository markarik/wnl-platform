<template>
	<form
		method="post"
		:action="paymentData.transaction_url"
		v-if="!isEmpty(userData) && !isEmpty(paymentData)"
	>
		<input
			type="hidden"
			name="p24_session_id"
			:value="paymentData.session_id"
		/>
		<input
			type="hidden"
			name="p24_pos_id"
			:value="paymentData.merchant_id"
		/>
		<input
			type="hidden"
			name="p24_merchant_id"
			:value="paymentData.merchant_id"
		/>
		<input
			type="hidden"
			name="p24_amount"
			:value="amount"
		/>
		<input
			type="hidden"
			name="p24_currency"
			value="PLN"
		/>
		<input
			type="hidden"
			name="p24_description"
			:value="description"
		/>
		<input
			type="hidden"
			name="p24_client"
			:value="this.currentUserProfile.full_name"
		/>
		<input
			type="hidden"
			name="p24_address"
			:value="userData.street"
		/>
		<input
			type="hidden"
			name="p24_zip"
			:value="userData.zip"
		/>
		<input
			type="hidden"
			name="p24_city"
			:value="userData.city"
		/>
		<input
			type="hidden"
			name="p24_country"
			value="PL"
		/>
		<input
			type="hidden"
			name="p24_email"
			:value="paymentData.user_email"
		/>
		<input
			type="hidden"
			name="p24_language"
			value="pl"
		/>
		<input
			type="hidden"
			name="p24_url_return"
			:value="returnUrl"
		/>
		<input
			type="hidden"
			name="p24_url_status"
			:value="paymentData.url_status"
		/>
		<input
			type="hidden"
			name="p24_api_version"
			:value="paymentData.api_version"
		/>
		<input
			type="hidden"
			name="p24_sign"
			:value="paymentData.checksum"
		/>
		<input
			type="hidden"
			name="p24_encoding"
			value="UTF-8"
		/>
		<slot></slot>
	</form>
</template>

<script>
import { mapGetters } from 'vuex';
import { isEmpty } from 'lodash';

export default {
	props: {
		paymentData: {
			type: Object,
			default: () => ({}),
		},
		userData: {
			type: Object,
			default: () => ({})
		},
		order: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			isEmpty
		};
	},
	computed: {
		...mapGetters(['currentUserProfile']),
		returnUrl() {
			return window.location.href;
		},
		amount() {
			return this.paymentData.amount * 100;
		},
		description() {
			return `Zamówienie: ${this.order.id}, ${this.order.product.name}`;
		}
	}
};
</script>
