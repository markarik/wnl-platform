<template>
	<wnl-rectangle-button :class="iconClass" @click="toggleReaction">
		<div>
			<span class="icon is-small">
				<i class="fa" :class="hasReactedClass" />
			</span>
			<span class="count">{{count}}</span>
			<span class="flash-bg" :class="{'flash': wasJustClicked}" />
		</div>
	</wnl-rectangle-button>

</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.vote
		border: 1px solid currentColor

		.count
			margin-left: $margin-small

		&.vote-up
			color: $color-green

		&.vote-down
			color: $color-red

	.flash-bg
		border-radius: 100px
		left: 50%
		position: absolute
		opacity: 0.5
		top: 50%

		&.flash
			animation: flash 0.2s ease-out

	@keyframes flash
		0%
			background: transparent
			height: 0
			width: 0
		20%
			background: $color-green
		100%
			background: transparent
			height: 50px
			left: -10px
			top: -10px
			width: 50px
</style>

<script>
import { reaction } from 'js/mixins/reaction';
import WnlRectangleButton from 'js/components/RecatangleButton';

export default {
	name: 'Vote',
	components: { WnlRectangleButton },
	mixins: [reaction],
	props: ['type'],
	data() {
		return {
			isLoading: false,
			name: 'upvote',
		};
	},
	computed: {
		iconClass() {
			return `vote vote-${this.type}`;
		},
		hasReactedClass() {
			return this.hasReacted ? 'fa-thumbs-up' : 'fa-thumbs-o-up';
		},
	},
};
</script>
