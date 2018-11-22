<template>
	<div class="subscription">
		<table class="table is-bordered is-narrow">
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
	</div>
</template>
<style lang="sass" scoped>
	.table
		max-width: 420px
</style>
<script>
	import moment from 'moment';

	export default {
		name: "UserSubscription",
		props: {
			user: {
				type: Object,
				required: true
			}
		},
		methods: {
			formatDate(date) {
				return moment(date * 1000).format('DD/MM/YY')
			}
		}
	}
</script>

<style scoped>

</style>
