<template>
	<div class="flashcards">
		<div class="flashcards-container" v-if="isReady">
			<wnl-flashcard-editor v-if="flashcardId" :flashcardId="flashcardId" />
			<wnl-flashcards-list v-else />
		</div>
	</div>
</template>

<script>
	import FlashcardsList from 'js/admin/components/flashcards/list/FlashcardsList.vue'
	import FlashcardEditor from 'js/admin/components/flashcards/edit/FlashcardEditor'
	import { mapState, mapActions } from 'vuex'

	export default {
		name: 'Flashcards',
		components: {
			'wnl-flashcards-list': FlashcardsList,
			'wnl-flashcard-editor': FlashcardEditor,
		},
		computed: {
			...mapState('flashcards', {
				isReady: 'ready'
			}),
			flashcardId() {
				return this.$route.params.flashcardId
			},
		},
		methods: {
			...mapActions('flashcards', ['setup']),
		},
		mounted() {
			this.setup()
		}
	}
</script>
