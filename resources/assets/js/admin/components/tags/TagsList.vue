<template>
	<div class="orders-list">
		<wnl-paginated-list
			:resourceUrl="getApiUrl('tags/.filter')"
		>
			<h3 slot="header">Lista Tagów</h3>
			<ul slot-scope="slotProps" slot="list">
				<li v-for="tag in slotProps.list" :key="tag.id" class="margin bottom">
					<router-link :to="{}">{{tag.id}}. {{tag.name}}</router-link>
				</li>
			</ul>
		</wnl-paginated-list>

	</div>
</template>

<script>
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';
import {getApiUrl} from 'js/utils/env';

export default {
	components: {
		WnlPaginatedList,
	},
	methods: {
		getApiUrl,
	},
};
</script>
