<template>
	<div class="flashcards-list">
		<h3 class="title">Lista zestawów pytań</h3>
		<router-link :to="{ name: 'flashcards-sets', params: { flashcardsSetId: 'new' } }" class="button is-success margin bottom">+ Nowy zestaw</router-link>
		<wnl-flashcards-sets-list-item v-for="flashcardsSet in allFlashcardsSets"
							  :key="flashcardsSet.id"
							  :name="flashcardsSet.name"
							  :id="flashcardsSet.id"
		/>
	</div>
</template>

<script>
	import {mapState} from 'vuex'

	import WnlFlashcardsSetsListItem from 'js/admin/components/flashcards/list/FlashcardsSetsListItem'

	export default {
		name: 'FlashcardsSetList',
		components: {
			WnlFlashcardsSetsListItem
		},
		computed: {
			...mapState('flashcardsSets', {
				allFlashcardsSets: 'flashcardsSets'
			})
		},
	}
</script>
