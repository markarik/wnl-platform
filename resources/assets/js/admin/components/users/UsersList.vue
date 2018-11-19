<template>
	<div class="annotations-users">
		<slot name="search"></slot>
		<ul v-if="users.length">
			<table class="users-users">
				<thead>
					<tr>
						<th>#</th>
						<th>Imię Nazwisko</th>
						<th>Rola</th>
						<th>Data stworzenia</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(user, index) in users" class="users-users__item">
						<td>{{ user.id }}</td>
						<td>{{ user.full_name }}</td>
						<td>{{ getRoles(user) }}</td>
						<td>{{ getDateCreated(user) }}</td>
					</tr>
				</tbody>
			</table>
		</ul>
		<div v-else>
			<span class="title is-6">Nic tu nie ma...</span>
		</div>
		<slot name="pagination"/>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.users-users
		&__item
			&:nth-child(even)
				background: $color-background-light-gray
			td
				padding: 5px

				&:last-child
					text-align: right
</style>

<script>
	import moment from 'moment'

	export default {
		name: 'UsersList',
		data() {
			return {
				openAnnotations: []
			}
		},
		props: {
			users: {
				type: Array,
				required: true
			},
			roles: {
				type: Array,
				required: true
			},
			modifiedAnnotationId: {
				type: Number,
				default: 0
			}
		},
		methods: {
			getRoles(user) {
				if (!user.hasOwnProperty('roles')) {
					return '';
				}
				return Object.values(user.roles).map(roleId => this.roles[roleId]).join(', ')
			},
			getDateCreated(user) {
				return moment(user.created_at * 1000).format('D MMM Y')
			}
		}
	}
</script>
