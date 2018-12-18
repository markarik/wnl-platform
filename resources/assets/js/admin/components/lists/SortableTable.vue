<template>
			<table class="table">
				<thead>
				<tr>
					<wnl-sortable-table-column-header
						v-for="column in columns"
						:key="column.name"
						@click="changeOrder(column.name)"
						:label="column.label"
						:is-active="column.name === activeSortColumnName"
						:sort-direction="sortDirection"
					/>
				</tr>
				</thead>
				<slot name="tbody" :list="list" />
			</table>
</template>

<style lang="sass">
	.row
		cursor: pointer
</style>

<script>
import WnlSortableTableColumnHeader from 'js/admin/components/lists/components/SortableTableColumnHeader';

export default {
	components: {
		WnlSortableTableColumnHeader
	},
	props: {
		columns: {
			type: Array,
			required: true,
		},
		activeSortColumnName: {
			type: String,
			required: true,
		},
		list: {
			type: Array|Object,
			required: true,
		},
		sortDirection: {
			type: String,
			default: 'asc',
		}
	},
	methods: {
		changeOrder(name) {
			this.$emit('changeOrder', {
				sortDirection: this.activeSortColumnName === name && this.sortDirection === 'asc' ? 'desc' : 'asc',
				activeSortColumnName: name
			});
		},
	},
};
</script>
