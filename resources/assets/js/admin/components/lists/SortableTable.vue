<template>
		<wnl-paginated-list
			:resourceUrl="resourceUrl"
			:customRequestParams="requestParams"
		>
			<template slot="header">
				<slot name="header"></slot>
			</template>

			<table class="table" slot-scope="slotProps" slot="list">
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
				<slot name="tbody" :list="slotProps.list" />
			</table>
		</wnl-paginated-list>
</template>

<style lang="sass">
	.row
		cursor: pointer
</style>

<script>
	import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';
	import WnlSortableTableColumnHeader from 'js/admin/components/lists/components/SortableTableColumnHeader';

	export default {
		components: {
			WnlPaginatedList,
			WnlSortableTableColumnHeader
		},
		data() {
			return {
				activeSortColumnName: this.columns[0].name,
				sortDirection: 'asc',
			}
		},
		props: {
			columns: {
				type: Array,
				required: true,
			},
			resourceUrl: {
				type: String,
				required: true,
			},
			searchAvailableFields: {
				type: Array,
				default: () => [],
			},
			customRequestParams: {
				type: Object,
				default: () => ({})
			}
		},
		computed: {
			requestParams() {
				return {
					order: {
						[this.activeSortColumnName]: this.sortDirection,
					},
					...this.customRequestParams,
				};
			}
		},
		methods: {
			changeOrder(name) {
				if (this.activeSortColumnName === name) {
					this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
				} else {
					this.sortDirection = 'asc';
					this.activeSortColumnName = name;
				}
			},
		},
	};
</script>
