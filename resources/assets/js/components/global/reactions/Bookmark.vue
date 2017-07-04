<template>
	<div v-if="!reactionsDisabled" class="bookmark" @click="toggleReaction">
		<span class="icon is-small">
			<i class="fa" :class="hasReactedClass"></i>
		</span>
		<span>Zapisz</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.bookmark
		align-items: center
		color: $color-sky-blue
		cursor: pointer
		display: flex
		font-size: $font-size-minus-2
		flex-direction: column
		text-transform: uppercase
		width: 50px
</style>

<script>
	import { reaction } from 'js/mixins/reaction'

	export default {
		name: 'Bookmark',
		mixins: [reaction],
		data() {
			return {
				isLoading: false,
				name: 'bookmark',
			}
		},
		computed: {
			hasReactedClass() {
				return this.hasReacted ? 'fa-bookmark' : 'fa-bookmark-o'
			},
		},
	}
</script>
