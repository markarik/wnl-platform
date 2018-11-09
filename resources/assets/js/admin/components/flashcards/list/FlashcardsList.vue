<template>
	<div class="flashcards-list">
		<router-link :to="{ name: 'flashcards', params: { flashcardId: 'new' } }" class="button is-success">+ Nowe pytanie</router-link>
		<p class="title is-4">Lista pytań</p>
		<wnl-flashcard-list-item v-for="flashcard in allFlashcards"
							  :key="flashcard.id"
							  :content="flashcard.content"
							  :id="flashcard.id">
		</wnl-flashcard-list-item>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapGetters} from 'vuex'

	import FlashcardsListItem from 'js/admin/components/flashcards/list/FlashcardsListItem'

	export default {
		name: 'FlashcardsList',
		components: {
			'wnl-flashcard-list-item': FlashcardsListItem,
		},
		computed: {
			...mapGetters('flashcards', ['allFlashcards'])
		},
	}
</script>
