<template>
	<div class="orders-list">
		<wnl-paginated-list
			:resourceName="'taxonomies/.filter'"
		>
			<h3 slot="header" class="title">
				Lista Taxonomii
				TODO
				<router-link class="button is-success" :to="{name: 'tag-edit', params: { id: 'new' } }">+ Dodaj taksonomię</router-link>
			</h3>

			<table class="table" slot-scope="slotProps" slot="list">
				<thead>
				<tr>
					<th>Id</th>
					<th>Nazwa</th>
				</tr>
				</thead>
				<tbody>
				<tr v-for="taxonomy in slotProps.list" :key="taxonomy.id" class="row" @click="goToTaxonomy(taxonomy)">
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
			// TODO
			this.$router.push({ name: 'tag-edit', params: { id: taxonomy.id } });
		},
	},
};
</script>
