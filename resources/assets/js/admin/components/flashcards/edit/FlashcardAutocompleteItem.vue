<template>
	<span class="flashcard-autocomplete-item">{{item.id}}. {{item.content}}</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.flashcard-autocomplete-item
		text-overflow: ellipsis
		overflow: hidden
		white-space: nowrap

</style>

<script>
export default {
	name: 'WnlFlashcardAutocompleteItem',
	props: ['item'],
};
</script>
