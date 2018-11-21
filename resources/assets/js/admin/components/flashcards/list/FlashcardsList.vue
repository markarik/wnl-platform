<template>
	<div class="flashcards-list">
		<h3 class="title">Lista pytań</h3>
		<router-link :to="{ name: 'flashcards-edit', params: { flashcardId: 'new' } }" class="button is-success margin bottom">+ Nowe pytanie</router-link>
		<wnl-flashcard-list-item v-for="flashcard in allFlashcards"
							  :key="flashcard.id"
							  :content="flashcard.content"
							  :id="flashcard.id">
		</wnl-flashcard-list-item>
	</div>
</template>

<script>
	import {mapState, mapActions} from 'vuex'

	import FlashcardsListItem from 'js/admin/components/flashcards/list/FlashcardsListItem'

	export default {
		name: 'FlashcardsList',
		components: {
			'wnl-flashcard-list-item': FlashcardsListItem,
		},
		computed: {
			...mapState('flashcards', {
				allFlashcards: 'flashcards',
			})
		},
		methods: {
			...mapActions('flashcards', ['setup']),
		},
		mounted() {
			this.setup()
		}
	}
</script>
