<template>
	<div class="orders-list">
		<wnl-sortable-table
			:resourceUrl="getApiUrl('tags/.filter')"
			:columns="columns"
			:customRequestParams="{include: 'taggables_count'}"
		>
			<h3 slot="header" class="title">Lista Tagów</h3>

			<tbody slot-scope="slotProps" slot="tbody">
			<tr v-for="tag in parseIncludes(slotProps.list)" :key="tag.id" class="row" @click="goToTag(tag)">
				<td>{{tag.id}}</td>
				<td>{{tag.name}}</td>
				<td>{{tag.taggables_count}}</td>
			</tr>
			</tbody>
		</wnl-sortable-table>
	</div>
</template>

<style lang="sass">
	.row
		cursor: pointer
</style>

<script>
import WnlSortableTable from 'js/admin/components/lists/SortableTable';
import {getApiUrl} from 'js/utils/env';

export default {
	components: {
		WnlSortableTable,
	},
	data() {
		return {
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
					name: 'taggables_count',
					label: 'Ilość powiązań',
				},
			]
		}
	},
	methods: {
		getApiUrl,
		goToTag(tag) {
			this.$router.push({ });
		},
		parseIncludes(data) {
			const {included, ...list} = data;

			return Object.values(list).map(item => {
				item.taggables_count = included.taggables_count[item.id].taggables_count;
				return item;
			});
		}
	},
};
</script>
