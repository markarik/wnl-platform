<template>
	<div class="groups">
		<wnl-paginated-list
				:resource-name="'groups/.filter'"
		>
			<h3 class="title is-3" slot="header">
				Edycja grup
				<router-link class="button is-success" :to="{name: 'group-edit', params: { id: 'new' } }">+ Dodaj grupę</router-link>
			</h3>
			<ul slot="list" slot-scope="slotParams">
				<wnl-groups-list-item v-for="group in slotParams.list" :id="group.id" :name="group.name" :key="group.id"/>
			</ul>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlGroupsListItem from 'js/admin/components/groups/GroupsListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'Groups',
	data() {
		return {
			groups: [],
			lastPage: 1,
			page: 1,
		};
	},
	components: {
		WnlGroupsListItem,
		WnlPaginatedList
	},
};
</script>
