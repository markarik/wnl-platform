<template>
	<div class="flashcards-list">
		<wnl-paginated-list
			:resource-name="'flashcards_sets/.filter'"
		>
			<template slot="header">
				<h3 class="title">Lista zestawów pytań otwartych</h3>
				<router-link
					:to="{ name: 'flashcards-sets-edit', params: { flashcardsSetId: 'new' } }"
					class="button is-success margin bottom"
				>+ Nowy zestaw</router-link>
			</template>
			<template slot="list" slot-scope="slotParams">
				<wnl-flashcards-sets-list-item
					v-for="flashcardsSet in slotParams.list"
					:id="flashcardsSet.id"
					:key="flashcardsSet.id"
					:name="flashcardsSet.name"
				/>
			</template>
		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlFlashcardsSetsListItem from 'js/admin/components/flashcards/list/FlashcardsSetsListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'FlashcardsSetList',
	components: {
		WnlFlashcardsSetsListItem,
		WnlPaginatedList
	},
};
</script>
