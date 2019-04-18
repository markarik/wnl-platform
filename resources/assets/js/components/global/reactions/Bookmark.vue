<template>
	<div
		v-if="!reactionsDisabled"
		class="bookmark"
		@click="toggleReaction"
	>
		<span v-if="!isLoading" class="icon is-small">
			<i class="fa" :class="hasReactedClass" />
		</span>
		<span v-else class="loader" />
		<span v-if="!isMobile">Zapisz</span>
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

	.loader
		height: 1rem
		width: 1rem
</style>

<script>
import { mapGetters } from 'vuex';

import { reaction } from 'js/mixins/reaction';

export default {
	name: 'Bookmark',
	mixins: [reaction],
	data() {
		return {
			isLoading: false,
			name: 'bookmark',
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		hasReactedClass() {
			return this.hasReacted ? 'fa-star' : 'fa-star-o';
		},
	},
};
</script>
