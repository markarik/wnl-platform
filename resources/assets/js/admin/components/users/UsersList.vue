<template>
	<div class="annotations-users">
		<ul>
			<table class="users-users">
				<thead>
				<tr>
					<th>#</th>
					<th>Imię Nazwisko</th>
					<th>Login</th>
					<th>Data stworzenia</th>
				</tr>
				</thead>
				<tbody>
				<tr v-for="user in users" class="users-users__item" @click="goToDetails(user.id)" :key="user.id">
					<td>{{ user.id }}</td>
					<td>
						{{ user.full_name }}
						<span
							class="tag"
							v-for="(role, index) in user.roles"
							:key="index"
							:style="{backgroundColor: getColourForStr(role.name)}">
						{{ role.name }}
						</span>
					</td>
					<td>{{ user.email }}</td>
					<td>{{ getCreatedDate(user) }}</td>
				</tr>
				</tbody>
			</table>
		</ul>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.users-users
		th
			padding: 10px

		&__item
			&:nth-child(even)
				background: $color-background-light-gray
			td
				padding: 10px

			&:hover
				cursor: pointer
				background: $color-ocean-blue-less-opacity

			.tag
				margin-left: $margin-small
</style>

<script>
import moment from 'moment';
import { getColourForStr } from 'js/utils/colors.js';

export default {
	name: 'UsersList',
	data() {
		return {
			getColourForStr
		};
	},
	props: {
		usersResponse: {
			type: Object|Array,
			required: true
		},
	},
	computed: {
		users() {
			const {included, ...users} = this.usersResponse;
			return Object.values(users).map(user => ({
				...user,
				roles: (user.roles || []).map(roleId => included.roles[roleId])
			}));
		}
	},
	methods: {
		getCreatedDate(user) {
			return moment(user.created_at * 1000).format('ll');
		},
		goToDetails(userId) {
			this.$router.push({ name: 'user-details', params: { userId } });
		}
	}
};
</script>
