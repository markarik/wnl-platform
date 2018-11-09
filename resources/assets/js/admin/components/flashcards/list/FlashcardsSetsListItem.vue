<template>
	<div class="margin bottom">
		<router-link :to="to">{{id}}. {{name}}</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
	export default {
		name: 'FlashcardsSetsListItem',
		props: ['id', 'name'],
		computed: {
			to() {
				return {
					name: 'flashcards-sets',
					params: {
						flashcardsSetId: this.id,
					},
				}
			},
		},
	}
</script>
