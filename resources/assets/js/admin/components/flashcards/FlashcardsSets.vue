<template>
	<div class="flashcards-sets">
		<div class="flashcards-container" v-if="isReady">
			<wnl-flashcards-set-editor v-if="flashcardsSetId" />
			<wnl-flashcards-sets-list v-else />
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import WnlFlashcardsSetsList from 'js/admin/components/flashcards/list/FlashcardsSetsList.vue'
	import WnlFlashcardsSetEditor from 'js/admin/components/flashcards/edit/FlashcardsSetEditor'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'Flashcards',
		components: {
			WnlFlashcardsSetsList,
			WnlFlashcardsSetEditor,
		},
		computed: {
			...mapGetters('flashcardsSets', ['isReady']),
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
