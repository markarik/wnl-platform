<template>
	<div class="orders-list">
		<wnl-paginated-list
			:resource-name="'taxonomies/.filter'"
		>
			<h3 slot="header" class="title">
				Lista Taxonomii
				<router-link class="button is-success" :to="{name: 'taxonomy-edit', params: { id: 'new' } }">+ Dodaj taksonomię</router-link>
			</h3>

			<table
				slot="list"
				slot-scope="slotProps"
				class="table"
			>
				<thead>
				<tr>
					<th>Id</th>
					<th>Nazwa</th>
				</tr>
				</thead>
				<tbody>
				<tr
					v-for="taxonomy in slotProps.list"
					:key="taxonomy.id"
					class="row"
					@click="goToTaxonomy(taxonomy)"
				>
					<td>{{taxonomy.id}}</td>
					<td>{{taxonomy.name}}</td>
				</tr>
				</tbody>
			</table>
		</wnl-paginated-list>
	</div>
</template>

<style lang="sass">
	.row
		cursor: pointer
</style>

<script>
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	components: {
		WnlPaginatedList,
	},
	methods: {
		goToTaxonomy(taxonomy) {
			this.$router.push({ name: 'taxonomy-edit', params: { id: taxonomy.id } });
		},
	},
};
</script>
