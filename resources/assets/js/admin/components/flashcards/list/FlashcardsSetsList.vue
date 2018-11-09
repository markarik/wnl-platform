<template>
	<div class="flashcards-list">
		<p class="title is-4">Lista pytań</p>
		<wnl-flashcards-sets-list-item v-for="flashcardsSet in allFlashcardsSets"
							  :key="flashcardsSet.id"
							  :name="flashcardsSet.name"
							  :id="flashcardsSet.id"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapGetters} from 'vuex'

	import WnlFlashcardsSetsListItem from 'js/admin/components/flashcards/list/FlashcardsSetsListItem'

	export default {
		name: 'FlashcardsSetList',
		components: {
			WnlFlashcardsSetsListItem
		},
		computed: {
			...mapGetters('flashcardsSets', ['allFlashcardsSets'])
		},
	}
</script>
