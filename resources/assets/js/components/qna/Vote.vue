<template>
	<div class="vote" :class="iconClass">
		<span class="icon is-small">
			<i class="fa fa-thumbs-o-up"></i>
		</span>
		<span class="count">{{count}}</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.vote
		align-items: center
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
	export default {
		name: 'Vote',
		props: ['type', 'count'],
		computed: {
			iconClass() {
				return `vote-${this.type}`
			},
		}
	}
</script>
