<template>
	<div class="margin bottom">
		<router-link :to="to">{{id}}. {{name}}</router-link>
	</div>
</template>

<script>
export default {
	name: 'FlashcardsSetsListItem',
	props: ['id', 'name'],
	computed: {
		to() {
			return {
				name: 'flashcards-sets-edit',
				params: {
					flashcardsSetId: this.id,
				},
			};
		},
	},
};
</script>
