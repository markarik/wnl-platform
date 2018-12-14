<template>
	<div class="subscription">
		<table class="table is-bordered is-narrow" v-if="user.subscription">
			<thead>
				<tr>
					<th>Dostęp Od</th>
					<th>Dostęp Do</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{formatDate(user.subscription.access_start)}}</td>
					<td>{{formatDate(user.subscription.access_end)}}</td>
				</tr>
			</tbody>
		</table>
		<p v-else>Ten użytkownik nie posiada subskrypcji do kursu</p>
	</div>
</template>
<style lang="sass" scoped>
	.table
		max-width: 420px
</style>
<script>
import moment from 'moment';

export default {
	name: 'UserSubscription',
	props: {
		user: {
			type: Object,
			required: true
		}
	},
	methods: {
		formatDate(date) {
			return moment(date * 1000).format('ll');
		}
	}
};
</script>

<style scoped>

</style>
