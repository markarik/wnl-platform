<template>
	<wnl-rectangle-button
		v-if="!reactionsDisabled"
		:class="{watch: true,'has-reacted': hasReacted}"
		@click="toggleReaction"
	>
		<span v-t="reactionMessage" />
		<span v-if="!isLoading" class="icon is-small watch-icon">
			<i class="fa" :class="hasReactedClass" />
		</span>
		<span v-else class="loader" />
	</wnl-rectangle-button>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.watch
		color: $color-gray
		&.has-reacted
			border-color: $color-green
			color: $color-green

	.watch-icon
		margin-left: $margin-small

	.loader
		height: 1rem
		margin-left: $margin-small
		width: 1rem
</style>

<script>
import { mapGetters } from 'vuex';

import { reaction } from 'js/mixins/reaction';
import WnlRectangleButton from 'js/components/RecatangleButton';

export default {
	name: 'Watch',
	components: { WnlRectangleButton },
	mixins: [reaction],
	data() {
		return {
			isLoading: false,
			name: 'watch'
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		hasReactedClass() {
			return this.hasReacted ? 'fa-check' : 'fa-eye';
		},
		reactionMessage() {
			return this.hasReacted ? 'ui.action.watching' : 'ui.action.watch';
		},
	}
};
</script>
