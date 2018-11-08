<template>
    <div class="margin bottom">
        <router-link :to="to">{{id}}. {{content}}</router-link>
    </div>
</template>

<style lang="sass" rel="stylesheet/sass">

</style>

<script>
	export default {
		name: 'FlashcardsListItem',
		props: ['id', 'content'],
		computed: {
			to() {
				return {
					name: 'flashcards',
					params: {
						flashcardId: this.id,
					},
				}
			},
		},
	}
</script>
