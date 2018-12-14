<template>
	<div class="flashcards-list" >
		<wnl-paginated-list
			:resource-url="getApiUrl('flashcards_sets/.filter')"
		>
			<template slot="header">
				<h3 class="title">Lista zestawów pytań otwartych</h3>
				<router-link
					:to="{ name: 'flashcards-sets-edit', params: { flashcardsSetId: 'new' } }"
					class="button is-success margin bottom"
				>+ Nowy zestaw</router-link>
			</template>
			<template slot="list" slot-scope="slotParams">
				<wnl-flashcards-sets-list-item v-for="flashcardsSet in slotParams.list"
					:key="flashcardsSet.id"
					:name="flashcardsSet.name"
					:id="flashcardsSet.id"
				/>
			</template>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlFlashcardsSetsListItem from 'js/admin/components/flashcards/list/FlashcardsSetsListItem';
import {getApiUrl} from 'js/utils/env';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'FlashcardsSetList',
	components: {
		WnlFlashcardsSetsListItem,
		WnlPaginatedList
	},
	methods: {
		getApiUrl,
	}
};
</script>
