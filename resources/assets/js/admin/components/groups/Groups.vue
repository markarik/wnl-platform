<template>
	<div class="groups">
		<wnl-paginated-list
			:resource-name="'groups/.filter'"
		>
			<h3 slot="header" class="title is-3">
				Edycja grup
				<router-link class="button is-success" :to="{name: 'group-edit', params: { id: 'new' } }">+ Dodaj grupę</router-link>
			</h3>
			<ul slot="list" slot-scope="slotParams">
				<wnl-groups-list-item
					v-for="group in slotParams.list"
					:id="group.id"
					:key="group.id"
					:name="group.name"
				/>
			</ul>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlGroupsListItem from 'js/admin/components/groups/GroupsListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'Groups',
	components: {
		WnlGroupsListItem,
		WnlPaginatedList
	},
	data() {
		return {
			groups: [],
			lastPage: 1,
			page: 1,
		};
	},
};
</script>
