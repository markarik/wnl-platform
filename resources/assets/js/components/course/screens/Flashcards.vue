<template>
	<div class="flashcards content">
		<div v-html="content"/>
		<ol class="flashcards-list">
			<li v-for="flashcard in flashcards" :key="flashcard.id" class="flashcards-list__item">
				<span class="flashcards-list__item__text">{{flashcard.content}}</span>
			</li>
		</ol>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.flashcards.content
		margin: $margin-big 0
		font-size: $font-size-plus-1
		line-height: $line-height-plus

		.flashcards-list
			counter-reset: li
			padding-left: 15px
			margin-left: 0
			list-style: decimal

			&__item
				list-style-type: none

				&:nth-child(5n)
					margin-bottom: $margin-big

				&:nth-child(10n-4),
				&:nth-child(10n-3),
				&:nth-child(10n-2),
				&:nth-child(10n-1),
				&:nth-child(10n)
					&:before
						color: $color-purple

				&:before
					color: $color-ocean-blue
					content: counter(li) ". "
					counter-increment: li
					font-weight: 700
</style>
<script>
	import {mapActions} from 'vuex';
	import {get} from 'lodash';

	export default {
		props: {
			screenData: {
				type: Object,
				required: true
			}
		},
		data() {
			return {
				content: '',
				flashcards: []
			}
		},
		methods: {
			...mapActions('flashcards', ['fetchFlashcardsSet']),
		},
		async mounted() {
			const setId = get(this.screenData, 'meta.resources[0].id');

			const flashcardsSetResponse = await this.fetchFlashcardsSet({
				setId: setId,
				include: 'flashcards'
			})

			this.content = flashcardsSetResponse.description;
			const flashcards = get(flashcardsSetResponse, 'included.flashcards')

			if (!flashcards) {
				$wnl.logger.error('flashcards not defined inside the response')
			} else {
				this.flashcards = flashcards
			}
		}
	}
</script>
