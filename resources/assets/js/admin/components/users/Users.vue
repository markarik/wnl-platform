<template>
	<div>
		<wnl-paginated-list
			:resource-name="'users/.filter'"
			:search-available-fields="searchAvailableFields"
			:custom-request-params="requestParams"
		>
			<router-link class="button is-primary margin bottom" :to="{'name': 'users-add'}" slot="header">Dodaj Użytkownika</router-link>
			<wnl-users-list :users-response="slotParams.list" slot="list" slot-scope="slotParams"/>
		</wnl-paginated-list>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.users__pagination .pagination-list
		justify-content: center
		margin-top: $margin-medium
</style>

<script>
import WnlUsersList from './UsersList';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	components: {WnlUsersList, WnlPaginatedList},
	data() {
		return {
			requestParams: { include: 'roles' },
			searchAvailableFields: [
				{value: 'id', title: 'ID'},
				{value: 'email', title: 'Email'},
				{value: 'full_name', title: 'Imię i nazwisko'},
			]
		};
	},
};
</script>
