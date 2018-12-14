<template>
	<div class="margin bottom">
		<router-link :to="to">{{id}}. {{content}}</router-link>
	</div>
</template>

<script>
export default {
	name: 'FlashcardsListItem',
	props: ['id', 'content'],
	computed: {
		to() {
			return {
				name: 'flashcards-edit',
				params: {
					flashcardId: this.id,
				},
			};
		},
	},
};
</script>
