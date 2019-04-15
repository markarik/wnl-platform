<template>
	<table class="table">
		<thead>
		<tr>
			<wnl-sortable-table-column-header
				v-for="column in columns"
				:key="column.name"
				@click="changeOrder(column)"
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
			type: [Array, Object],
			required: true,
		},
		sortDirection: {
			type: String,
			default: 'asc',
		}
	},
	computed: {
		routerSortDirection() {
			return this.$route.query.sortDirection || 'asc';
		},
		routerSort() {
			return this.$route.query.sort;
		},
	},
	methods: {
		changeOrder({ name, sortable = true }) {
			if (!sortable) return;
			const sortDirection = this.activeSortColumnName === name && this.sortDirection === 'asc' ? 'desc' : 'asc';

			this.$emit('changeOrder', {
				sortDirection,
				activeSortColumnName: name,
			});

			this.$router.push({ query: { ...this.$route.query, sortDirection, sort: name }, hash: this.$route.hash });
		},
	},
	mounted() {
		if (this.routerSort && (this.routerSortDirection !== this.sortDirection || this.routerSort !== this.activeSortColumnName)) {
			this.$emit('changeOrder', {
				sortDirection: this.routerSortDirection,
				activeSortColumnName: this.routerSort,
			});
		}
	},
	watch: {
		sortDirection(newVal) {
			if (this.routerSortDirection !== newVal) {
				this.$router.push({ query: { ...this.$route.query, sortDirection: newVal } });
			}
		},
		activeSortColumnName(newVal) {
			if (this.routerSort !== newVal) {
				this.$router.push({ query: { ...this.$route.query, sort: newVal } });
			}
		}
	}
};
</script>
