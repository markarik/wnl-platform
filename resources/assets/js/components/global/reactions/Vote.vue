<template>
	<div class="vote" :class="iconClass" @click="toggleReaction">
		<span class="icon is-small">
			<i class="fa" :class="hasReactedClass"></i>
		</span>
		<span class="count">{{ count }}</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.vote
		align-items: center
		cursor: pointer
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		display: flex

	.vote-up
		color: $color-green
		flex-direction: column

	.vote-down
		color: $color-red
		flex-direction: column-reverse
</style>

<script>
	import { reaction } from 'js/mixins/reaction'

	export default {
		name: 'Vote',
		mixins: [reaction],
		props: ['type'],
		data() {
			return {
				isLoading: false,
				name: 'upvote'
			}
		},
		computed: {
			iconClass() {
				return `vote-${this.type}`
			},
			hasReactedClass() {
				return this.reaction.hasReacted ? 'fa-thumbs-up' : 'fa-thumbs-o-up'
			},
		},
	}
</script>
