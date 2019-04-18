<template>
	<div>
		<h3 class="title is-3">
			Produkty
			<router-link
				class="button is-primary"
				:to="{ name: 'product-edit', params: { id: 'new' } }"
			>
				+ Dodaj nowy produkt
			</router-link>
		</h3>

		<wnl-paginated-sortable-table
			:is-search-enabled="false"
			:resource-name="'products/.filter'"
			:columns="columns"
			class="products"
		>
			<tbody slot="tbody" slot-scope="slotProps">
			<tr
				v-for="product in slotProps.list"
				:key="product.id"
				class="clickable"
				@click="goToEdit(product.id)"
			>
				<td>{{product.id}}</td>
				<td>{{product.name}}</td>
				<td>{{product.quantity}}/{{product.initial}}</td>
				<td>{{formatDate(product.signups_start)}}</td>
				<td>{{formatDate(product.created_at)}}</td>
			</tr>
			</tbody>
		</wnl-paginated-sortable-table>
	</div>
</template>

<script>
import { mapActions } from 'vuex';
import moment from 'moment';
import WnlPaginatedSortableTable from 'js/admin/components/lists/PaginatedSortableTable';

export default {
	components: {
		WnlPaginatedSortableTable,
	},
	data() {
		return {
			products: [],
			columns: [
				{
					name: 'id',
					label: 'Id',
				},
				{
					name: 'name',
					label: 'Nazwa',
				},
				{
					name: 'quantity',
					label: 'Dostępność',
					sortable: false
				},
				{
					name: 'signups_start',
					label: 'Start zapisów',
				},
				{
					name: 'created_at',
					label: 'Utworzono',
				},
			],
		};
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		formatDate(date) {
			if (date) {
				return moment(date * 1000).format('L LT');
			}
		},
		goToEdit(id) {
			this.$router.push({ name: 'product-edit', params: { id } });
		},
	}
};
</script>
