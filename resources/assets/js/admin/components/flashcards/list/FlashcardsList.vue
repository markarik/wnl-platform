<template>
	<div class="flashcards-list">
		<wnl-paginated-list
			:resource-name="'flashcards/.filter'"
		>
			<template slot="header">
				<h3 class="title">Lista pytań otwartych</h3>
				<router-link
					:to="{ name: 'flashcards-edit', params: { flashcardId: 'new' } }"
					class="button is-success margin bottom"
				>+ Nowe pytanie</router-link>
			</template>
			<template
				slot="list"
				slot-scope="slotParams"
			>
				<wnl-flashcards-list-item
					v-for="flashcard in slotParams.list"
					:id="flashcard.id"
					:key="flashcard.id"
					:content="flashcard.content"
				/>
			</template>

		</wnl-paginated-list>
	</div>
</template>

<script>
import WnlFlashcardsListItem from 'js/admin/components/flashcards/list/FlashcardsListItem';
import WnlPaginatedList from 'js/admin/components/lists/PaginatedList';

export default {
	name: 'FlashcardsList',
	components: {
		WnlFlashcardsListItem,
		WnlPaginatedList
	},
};
</script>
