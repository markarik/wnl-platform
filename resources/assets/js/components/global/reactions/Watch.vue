<template>
	<div v-if="!reactionsDisabled" class="watch" @click="toggleReaction">
		<span v-if="!isLoading" class="icon is-small watch-icon">
			<i class="fa" :class="hasReactedClass"></i>
		</span>
		<span v-else class="loader"></span>
		<span v-if="!isMobile">Obserwuj</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.watch
		align-items: center
		color: $color-sky-blue
		cursor: pointer
		display: flex
		font-size: $font-size-minus-2
		flex-direction: column
		margin-left: 4px
		margin-right: 4px
		text-transform: uppercase
	
	.watch-icon
		margin-right: 4px

	.loader
		height: 1rem
		width: 1rem
</style>

<script>
	import { mapGetters } from 'vuex'

	import { reaction } from 'js/mixins/reaction'

	export default {
		name: 'Watch',
		mixins: [reaction],
		data() {
			return {
				isLoading: false,
				name: 'watch',
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			hasReactedClass() {
				return this.hasReacted ? 'fa-eye-slash' : 'fa-eye'
			},
		},
	}
</script>
