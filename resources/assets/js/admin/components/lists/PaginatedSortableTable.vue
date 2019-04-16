<template>
	<wnl-paginated-list
		:resource-name="resourceName"
		:custom-request-params="requestParams"
		:is-search-enabled="isSearchEnabled"
	>
		<template slot="header">
			<slot name="header" />
		</template>

		<wnl-sortable-table
			slot="list"
			slot-scope="slotProps"
			:columns="columns"
			:active-sort-column-name="activeSortColumnName"
			:sort-direction="sortDirection"
			:list="slotProps.list"
			@changeOrder="changeOrder"
		>
			<template slot="tbody" slot-scope="tableProps">
				<slot name="tbody" :list="tableProps.list" />
			</template>
		</wnl-sortable-table>

	</wnl-paginated-list>
</template>

<script>
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';
import WnlSortableTable from 'js/admin/components/lists/SortableTable';

export default {
	components: {
		WnlPaginatedList,
		WnlSortableTable,
	},
	props: {
		columns: {
			type: Array,
			required: true,
		},
		resourceName: {
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
		},
		isSearchEnabled: {
			type: Boolean,
			default: true,
		}
	},
	data() {
		return {
			activeSortColumnName: this.columns[0].name,
			sortDirection: 'asc',
		};
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
		changeOrder({ activeSortColumnName, sortDirection }) {
			this.sortDirection = sortDirection;
			this.activeSortColumnName = activeSortColumnName;
		},
	},
};
</script>
