<template>
	<div class="subscription">
		<table v-if="user.coupons.length" class="table is-bordered is-narrow">
			<thead>
			<tr>
				<th>Nazwa</th>
				<th>Kod</th>
				<th>Wartość</th>
				<th>Pozostało użyć</th>
				<th>Data wygaśnięcia</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="coupon in user.coupons" :key="coupon.id">
				<td>{{coupon.name}}</td>
				<td>{{coupon.code}}</td>
				<td>{{getCouponValue(coupon)}}</td>
				<td>{{coupon.times_usable}}</td>
				<td>{{formatDate(coupon.expires_at)}}</td>
			</tr>
			</tbody>
		</table>
		<p v-else>Ten użytkownik nie posiada przypisanych kuponów</p>
	</div>
</template>

<style lang="sass" scoped>
	.table
		max-width: 100%
</style>
<script>
import moment from 'moment';

export default {
	name: 'UserCoupons',
	props: {
		user: {
			type: Object,
			required: true
		}
	},
	methods: {
		formatDate(date) {
			return moment(date * 1000).format('ll');
		},
		getCouponValue(coupon) {
			if (coupon.type === 'percentage') return `${coupon.value}%`;
			return `${coupon.value}zł`;
		}
	}
};
</script>

<style scoped>

</style>
