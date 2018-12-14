<template>
	<div class="groups">
		<h3 class="title is-3">
			Edycja grup
			<router-link class="button is-success" :to="{name: 'group-edit', params: { id: 'new' } }">+ Dodaj grupę</router-link>
		</h3>
		<wnl-pagination
				v-if="lastPage > 1"
				:currentPage="page"
				:lastPage="lastPage"
				@changePage="onPageChange"
				class="pagination"
		/>
		<ul>
			<wnl-groups-list-item v-for="group in groups" :id="group.id" :name="group.name" :key="group.id"/>
		</ul>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.pagination /deep/ .pagination-list
		justify-content: center
		margin-bottom: $margin-medium
</style>

<script>
import WnlPagination from 'js/components/global/Pagination';
import WnlGroupsListItem from 'js/admin/components/groups/GroupsListItem';
import {getApiUrl} from 'js/utils/env';

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
		WnlPagination,
		WnlGroupsListItem
	},
	computed: {},
	methods: {
		getRequestParams() {
			return {
				limit: 50,
				page: this.page,
				active: [],
				filters: []
			};
		},
		onPageChange(page) {
			this.page = page;
			this.fetch();
		},
		async fetch() {
			const response = await axios.post(getApiUrl('groups/.filter'), this.getRequestParams());
			const {data: {data, ...paginationMeta}} = response;
			this.groups = data;
			this.lastPage = paginationMeta.last_page;
		},
	},
	mounted() {
		this.fetch();
	}
};
</script>
