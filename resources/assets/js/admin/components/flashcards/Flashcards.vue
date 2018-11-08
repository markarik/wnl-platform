<template>
    <div class="flashcards">
        <p class="title is-3">Edycja powtórki</p>
        <div class="flashcards-container" v-if="isReady">
            <wnl-flashcard-editor v-if="flashcardId" />
            <wnl-flashcards-list v-else />
        </div>
    </div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import FlashcardsList from 'js/admin/components/flashcards/list/FlashcardsList.vue'
    import FlashcardEditor from 'js/admin/components/flashcards/edit/FlashcardEditor'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'Flashcards',
		components: {
			'wnl-flashcards-list': FlashcardsList,
            'wnl-flashcard-editor': FlashcardEditor,
		},
		computed: {
			...mapGetters('flashcards', ['isReady']),
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
