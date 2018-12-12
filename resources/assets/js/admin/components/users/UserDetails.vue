<template>
	<div class="user-details">
		<wnl-text-loader v-if="isLoading"></wnl-text-loader>
		<div v-else>
			<div class="user-details__head">
				<p>#{{ user.id }}</p>
				<p class="user-details__head__name">{{ user.full_name }}</p>
				<span
					class="tag"
					v-for="role in user.roles"
					:key="role.name"
					:style="{backgroundColor: getColourForStr(role.name)}">
					{{ role.name }}
				</span>
				<p>Dołączył/-a: {{ dateCreated }}</p>
			</div>

			<div class="tabs">
				<ul>
					<li :class="{ 'is-active': tab.active }" @click="changeTab(name)" v-for="(tab, name) in tabs" :key="name">
						<a>{{ tab.text }}</a>
					</li>
				</ul>
			</div>

			<component :is="activeComponent" :user="user"></component>

		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.user-details
		&__head
			margin-bottom: $margin-medium

			&__name
				font-size: $font-size-plus-2
				font-weight: bold

			.tag
				margin: $margin-small $margin-small $margin-small 0

</style>

<script>
import _ from 'lodash';
import axios from 'axios';
import {mapActions} from 'vuex';
import { getApiUrl } from 'js/utils/env';
import { getColourForStr } from 'js/utils/colors.js';
import moment from 'moment';
import UserSummary from './UserSummary';
import UserAddress from './UserAddress';
import UserCoupons from './UserCoupons';
import UserSubscription from './UserSubscription';
import UserOrders from './UserOrders';
import UserPlan from './UserPlan';
import UserBilling from './UserBilling';

export default {
	name: 'UserDetails',
	components: {},
	data() {
		return {
			getColourForStr,
			isLoading: true,
			user: {},
			tabs: {
				summary: {
					component: UserSummary,
					active: true,
					text: 'Podsumowanie'
				},
				address: {
					component: UserAddress,
					active: false,
					text: 'Dane do wysyłki'
				},
				billing: {
					component: UserBilling,
					active: false,
					text: 'Dane do faktury'
				},
				subscritption: {
					component: UserSubscription,
					active: false,
					text: 'Dostęp do kursu'
				},
				orders: {
					component: UserOrders,
					active: false,
					text: 'Zamówienia'
				},
				coupons: {
					component: UserCoupons,
					active: false,
					text: 'Kupony'
				},
				plan: {
					component: UserPlan,
					active: false,
					text: 'Plan lekcji'
				},
			},
		};
	},
	computed: {
		activeTab() {
			return Object.values(this.tabs).find(tab => tab.active);
		},
		activeComponent() {
			return this.activeTab.component;
		},
		dateCreated() {
			return moment(this.user.created_at * 1000).format('ll');
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		async setup() {
			const userId = this.$route.params.userId;
			try {
				const include = [
					'roles', 'profile', 'subscription', 'orders.invoices', 'billing', 'settings', 'coupons','user_address', 'orders.payments'
				].join(',');
				const response = await axios.get(getApiUrl(`users/${userId}?include=${include}`));
				const {included, ...user} = response.data;
				this.user = user;
				this.parseIncluded(included);
			} catch (error) {
				this.addAutoDismissableAlert({
					text: 'Oooops, coś poszło nie tak...',
					type: 'error'
				});
				$wnl.logger.capture(error);
			} finally {
				this.isLoading = false;
			}
		},
		parseIncluded(included){
			this.user.orders = _.reverse(Object.values(_.get(included, 'orders', {})))
				.map(order => {
					return {
						...order,
						invoices: (order.invoices || []).map(invoiceId => included.invoices[invoiceId]),
						payments: (order.payments || []).map(paymentId => included.payments[paymentId])
					};
				});
			this.user.roles = (this.user.roles || []).map(roleId => included.roles[roleId]);
			this.user.coupons = (this.user.coupons || []).map(couponId => included.coupons[couponId]);
			this.user.profile = this.user.profile && included.profile[this.user.profile[0]];
			this.user.user_address = this.user.user_address && included.user_address[this.user.user_address[0]];
			this.user.billing = this.user.billing && included.billing[this.user.billing[0]];
			this.user.settings = this.user.settings &&  included.settings[this.user.settings[0]];
			this.user.subscription = this.user.subscription && included.subscription[this.user.subscription[0]];
		},
		changeTab(name) {
			this.activeTab.active = false;
			this.tabs[name].active = true;
		},
	},
	async mounted() {
		await this.setup();
	}
};
</script>
