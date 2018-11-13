<template>
	<div class="flashcards-sets">
		<div class="flashcards-container" v-if="isReady">
			<wnl-flashcards-set-editor v-if="flashcardsSetId" :flashcardsSetId="flashcardsSetId" />
			<wnl-flashcards-sets-list v-else />
		</div>
	</div>
</template>

<script>
	import WnlFlashcardsSetsList from 'js/admin/components/flashcards/list/FlashcardsSetsList.vue'
	import WnlFlashcardsSetEditor from 'js/admin/components/flashcards/edit/FlashcardsSetEditor'
	import { mapState, mapActions } from 'vuex'

	export default {
		name: 'Flashcards',
		components: {
			WnlFlashcardsSetsList,
			WnlFlashcardsSetEditor,
		},
		computed: {
			...mapState('flashcardsSets', {
				isReady: 'ready'
			}),
			flashcardsSetId() {
				return this.$route.params.flashcardsSetId
			},
		},
		methods: {
			...mapActions('flashcardsSets', ['setup']),
		},
		mounted() {
			this.setup()
		}
	}
</script>
