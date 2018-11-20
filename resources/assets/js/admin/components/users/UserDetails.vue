<template>
	<div class="user-details">
		<wnl-text-loader v-if="isLoading"></wnl-text-loader>

		<div v-else>

			<div class="user-details__head">
				<p>#{{ user.id }}</p>
				<p class="user-details__head__name">{{ user.full_name }}</p>
				<span
					class="tag"
					v-for="role in roles"
					:style="{backgroundColor: getColourForStr(role)}">
					{{role}}
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

			<component :is="activeComponent">

			</component>

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
	import axios from 'axios';
	import {mapActions} from 'vuex'
	import { getApiUrl } from 'js/utils/env'
	import string_color from 'js/admin/mixins/string-color'
	import moment from 'moment'
	import UserSummary from './UserSummary'
	import UserAddress from './UserAddress'
	import UserBillingData from './UserBillingData'
	import UserSubscription from './UserSubscription'
	import UserOrders from './UserOrders'
	import UserPlan from './UserPlan'

	export default {
		name: "UserDetails",
		components: {},
		mixins: [ string_color ],
		data() {
			return {
				isLoading: true,
				user: {},
				roleNames: [],
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
						component: UserBillingData,
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
					plan: {
						component: UserPlan,
						active: false,
						text: 'Plan lekcji'
					}
				},
			}
		},
		computed: {
			activeTab() {
				return Object.values(this.tabs).find(tab => tab.active)
			},
			activeComponent() {
				return this.activeTab.component
			},
			dateCreated() {
				return moment(this.user.created_at * 1000).format('D MMM Y')
			},
			roles() {
				if (!this.user.hasOwnProperty('roles')) {
					return '';
				}
				return Object.values(this.user.roles).map(roleId => this.roleNames[roleId])
			},
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			async setup() {
				const userId = this.$route.params.userId
				try {
					const response = await axios.get(getApiUrl(`users/${userId}?include=roles,profile`))
					const {included, ...user} = response.data
					this.user = user
					Object.values(included.roles).forEach(role => {
						this.roleNames[role.id] = role.name
					})
				} catch (e) {
					this.addAutoDismissableAlert({
						text: "Oooops, coś poszło nie tak...",
						type: 'error'
					})
					console.error(e)
				} finally {
					this.isLoading = false
				}
			},
			changeTab(name) {
				this.activeTab.active = false;
				this.tabs[name].active = true;
			},
		},
		async mounted() {
			await this.setup()
		}
	}
</script>
